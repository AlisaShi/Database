import requests
import sqlalchemy
from sqlalchemy import create_engine, MetaData, Table, Column, Integer, String, Date, ForeignKey, ForeignKeyConstraint
from sqlalchemy.dialects.mysql import insert as mysql_insert
from sqlalchemy.orm import sessionmaker
from datetime import datetime

# Create SQLAlchemy engine and session
engine = create_engine('mysql+pymysql://root@localhost:3306/test05')
Session = sessionmaker(bind=engine)
session = Session()

# URL for TR_info data
tr_info_url = "https://recreation.forest.gov.tw/mis/api/OpenStatus/Trail"

def fetch_data(url):
    try:
        response = requests.get(url)
        response.raise_for_status()  # Raise HTTPError for bad responses
        return response.json()
    except requests.exceptions.RequestException as e:
        print(f"Failed to fetch data from {url}: {e}")
        return None

def get_tr_id(dep_name):
    result = session.execute(sqlalchemy.text("SELECT TR_ID FROM tr_admin WHERE TR_Name = :dep_name"), {'dep_name': dep_name}).fetchone()
    return int(result[0]) if result else None

def convert_date_format(date_str):
    if not date_str:
        return None
    return datetime.strptime(date_str, '%Y%m%d').date()

def parse_tr_info_data(data):
    if not data:
        print("No data found")
        return []

    parsed_data = []
    for item in data:
        trail_id = item.get('TRAILID', '')
        tr_typ = item.get('TR_TYP', '')
        title = item.get('TITLE', '')
        content = item.get('CONTENT', '')
        ann_date = convert_date_format(item.get('ANN_DATE', ''))
        open_date = convert_date_format(item.get('opendate', ''))
        close_date = convert_date_format(item.get('closedate', ''))
        tr_sub = item.get('TR_SUB', '')
        tr_id = get_tr_id(item.get('DEP_NAME', ''))

        new_entry = {
            'TRAILID': trail_id,
            'TR_TYP': tr_typ,
            'TITLE': title,
            'CONTENT': content,
            'ANN_DATE': ann_date,
            'opendate': open_date,
            'closedate': close_date,
            'TR_SUB': tr_sub,
            'TR_ID': tr_id
        }
        parsed_data.append(new_entry)

    return parsed_data

metadata = MetaData()

tr_info = Table('TR_info', metadata,
    Column('TRAILID', String(4), primary_key=True),
    Column('TR_TYP', String(20)),
    Column('TITLE', String(255)),
    Column('CONTENT', String(255)),
    Column('ANN_DATE', Date),
    Column('opendate', Date),
    Column('closedate', Date),
    Column('TR_SUB', String(20)),
    Column('TR_ID', Integer, ForeignKey('tr_admin.TR_ID')),
    ForeignKeyConstraint(['TRAILID'], ['trail.TRAILID'])
)

# Create the table if it doesn't exist
metadata.create_all(engine)

session.execute(sqlalchemy.text("DELETE FROM tr_info"))
session.commit()
print("tr_info table cleared")

# Fetch and parse data for TR_info
tr_info_data = fetch_data(tr_info_url)
parsed_tr_info_data = parse_tr_info_data(tr_info_data)

# Insert data into the TR_info table
for entry in parsed_tr_info_data:
    stmt = mysql_insert(tr_info).values(entry)
    stmt = stmt.on_duplicate_key_update(
        TR_TYP=stmt.inserted.TR_TYP,
        TITLE=stmt.inserted.TITLE,
        CONTENT=stmt.inserted.CONTENT,
        ANN_DATE=stmt.inserted.ANN_DATE,
        opendate=stmt.inserted.opendate,
        closedate=stmt.inserted.closedate,
        TR_SUB=stmt.inserted.TR_SUB,
        TR_ID=stmt.inserted.TR_ID
    )
    session.execute(stmt)

session.commit()
print("TR_info data inserted successfully")
