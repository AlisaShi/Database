import requests
import pandas as pd
import sqlalchemy
from sqlalchemy import create_engine
from sqlalchemy.orm import sessionmaker
from sqlalchemy import MetaData, Table, Column, Integer, String, DateTime, ForeignKey, ForeignKeyConstraint
from sqlalchemy.dialects.mysql import insert as mysql_insert
from datetime import datetime

# Create SQLAlchemy engine and session
engine = create_engine('mysql+pymysql://root@localhost:3306/test05')
Session = sessionmaker(bind=engine)
session = Session()

# URLs from the uploaded file
urls = [
    "https://opendata.cwa.gov.tw/fileapi/v1/opendataapi/F-D0047-003?Authorization=CWA-7A0C30DF-6FB4-429D-9142-4C1155C905D3&downloadType=WEB&format=JSON",
    "https://opendata.cwa.gov.tw/fileapi/v1/opendataapi/F-D0047-007?Authorization=CWA-7A0C30DF-6FB4-429D-9142-4C1155C905D3&downloadType=WEB&format=JSON",
    "https://opendata.cwa.gov.tw/fileapi/v1/opendataapi/F-D0047-011?Authorization=CWA-7A0C30DF-6FB4-429D-9142-4C1155C905D3&downloadType=WEB&format=JSON",
    "https://opendata.cwa.gov.tw/fileapi/v1/opendataapi/F-D0047-015?Authorization=CWA-7A0C30DF-6FB4-429D-9142-4C1155C905D3&downloadType=WEB&format=JSON",
    "https://opendata.cwa.gov.tw/fileapi/v1/opendataapi/F-D0047-019?Authorization=CWA-7A0C30DF-6FB4-429D-9142-4C1155C905D3&downloadType=WEB&format=JSON",
    "https://opendata.cwa.gov.tw/fileapi/v1/opendataapi/F-D0047-023?Authorization=CWA-7A0C30DF-6FB4-429D-9142-4C1155C905D3&downloadType=WEB&format=JSON",
    "https://opendata.cwa.gov.tw/fileapi/v1/opendataapi/F-D0047-027?Authorization=CWA-7A0C30DF-6FB4-429D-9142-4C1155C905D3&downloadType=WEB&format=JSON",
    "https://opendata.cwa.gov.tw/fileapi/v1/opendataapi/F-D0047-031?Authorization=CWA-7A0C30DF-6FB4-429D-9142-4C1155C905D3&downloadType=WEB&format=JSON",
    "https://opendata.cwa.gov.tw/fileapi/v1/opendataapi/F-D0047-035?Authorization=CWA-7A0C30DF-6FB4-429D-9142-4C1155C905D3&downloadType=WEB&format=JSON",
    "https://opendata.cwa.gov.tw/fileapi/v1/opendataapi/F-D0047-039?Authorization=CWA-7A0C30DF-6FB4-429D-9142-4C1155C905D3&downloadType=WEB&format=JSON",
    "https://opendata.cwa.gov.tw/fileapi/v1/opendataapi/F-D0047-043?Authorization=CWA-7A0C30DF-6FB4-429D-9142-4C1155C905D3&downloadType=WEB&format=JSON",
    "https://opendata.cwa.gov.tw/fileapi/v1/opendataapi/F-D0047-047?Authorization=CWA-7A0C30DF-6FB4-429D-9142-4C1155C905D3&downloadType=WEB&format=JSON",
    "https://opendata.cwa.gov.tw/fileapi/v1/opendataapi/F-D0047-051?Authorization=CWA-7A0C30DF-6FB4-429D-9142-4C1155C905D3&downloadType=WEB&format=JSON",
    "https://opendata.cwa.gov.tw/fileapi/v1/opendataapi/F-D0047-055?Authorization=CWA-7A0C30DF-6FB4-429D-9142-4C1155C905D3&downloadType=WEB&format=JSON",
    "https://opendata.cwa.gov.tw/fileapi/v1/opendataapi/F-D0047-059?Authorization=CWA-7A0C30DF-6FB4-429D-9142-4C1155C905D3&downloadType=WEB&format=JSON",
    "https://opendata.cwa.gov.tw/fileapi/v1/opendataapi/F-D0047-063?Authorization=CWA-7A0C30DF-6FB4-429D-9142-4C1155C905D3&downloadType=WEB&format=JSON",
    "https://opendata.cwa.gov.tw/fileapi/v1/opendataapi/F-D0047-067?Authorization=CWA-7A0C30DF-6FB4-429D-9142-4C1155C905D3&downloadType=WEB&format=JSON",
    "https://opendata.cwa.gov.tw/fileapi/v1/opendataapi/F-D0047-071?Authorization=CWA-7A0C30DF-6FB4-429D-9142-4C1155C905D3&downloadType=WEB&format=JSON",
    "https://opendata.cwa.gov.tw/fileapi/v1/opendataapi/F-D0047-075?Authorization=CWA-7A0C30DF-6FB4-429D-9142-4C1155C905D3&downloadType=WEB&format=JSON",
    "https://opendata.cwa.gov.tw/fileapi/v1/opendataapi/F-D0047-079?Authorization=CWA-7A0C30DF-6FB4-429D-9142-4C1155C905D3&downloadType=WEB&format=JSON",
    "https://opendata.cwa.gov.tw/fileapi/v1/opendataapi/F-D0047-083?Authorization=CWA-7A0C30DF-6FB4-429D-9142-4C1155C905D3&downloadType=WEB&format=JSON",
    "https://opendata.cwa.gov.tw/fileapi/v1/opendataapi/F-D0047-087?Authorization=CWA-7A0C30DF-6FB4-429D-9142-4C1155C905D3&downloadType=WEB&format=JSON",
]

def fetch_data(url):
    try:
        response = requests.get(url)
        response.raise_for_status()  # Raise HTTPError for bad responses
        return response.json()
    except requests.exceptions.RequestException as e:
        print(f"Failed to fetch data from {url}: {e}")
        return None

def get_district_id(location_name):
    result = session.execute(sqlalchemy.text("SELECT District_ID FROM district WHERE District = :location_name"), {'location_name': location_name}).fetchone()
    return result[0] if result else None

def get_weather_type_id(element_name):
    result = session.execute(sqlalchemy.text("SELECT Weather_Type_ID FROM weather_types WHERE Weather_Type = :element_name"), {'element_name': element_name}).fetchone()
    return result[0] if result else None


def convert_time_format(time_str):
    dt = datetime.fromisoformat(time_str[:-6])  # Remove timezone part and parse date-time
    return dt.strftime('%Y-%m-%d %H:%M:%S')

def parse_data(data):
    locations = data.get('cwaopendata', {}).get('dataset', {}).get('locations', {}).get('location', [])
    if not locations:
        print("No locations found in data")
        return []

    tmp = []
    for location in locations:
        district_id = get_district_id(location.get('locationName', ''))
        if not district_id:
            continue
        weather_elements = location.get('weatherElement', [])

        for weather_element in weather_elements:
            element_name = weather_element.get('elementName', '')
            time_periods = weather_element.get('time', [])

            for time_period in time_periods:
                start_time = convert_time_format(time_period.get('startTime', ''))
                end_time = convert_time_format(time_period.get('endTime', ''))

                element_values = time_period.get('elementValue', [])

                if isinstance(element_values, dict):  # When elementValue is an object
                    element_value = element_values.get('value', '')
                elif isinstance(element_values, list) and element_values:  # When elementValue is a list
                    element_value = element_values[0].get('value', '') if element_values else ''
                else:
                    element_value = ''

                existing_entry = next((entry for entry in tmp if entry['Start_Time'] == start_time and entry['End_Time'] == end_time and entry['District_ID'] == district_id), None)

                if existing_entry:
                    if element_name == "MaxT":
                        existing_entry['MaxTemperature'] = element_value
                    elif element_name == "MinT":
                        existing_entry['MinTemperature'] = element_value
                    elif element_name == "Wx":
                        existing_entry['Weather_Type_ID'] = get_weather_type_id(element_value)
                else:
                    new_entry = {
                        'Start_Time': start_time,
                        'End_Time': end_time,
                        'District_ID': district_id,
                        'Weather_Type_ID': get_weather_type_id(element_value) if element_name == "Wx" else None,
                        'MaxTemperature': element_value if element_name == "MaxT" else None,
                        'MinTemperature': element_value if element_name == "MinT" else None,
                        'Remarks': None
                    }
                    tmp.append(new_entry)
    return tmp

metadata = MetaData()

weather_forecast = Table('weather_forecast', metadata,
    Column('Start_Time', DateTime, primary_key=True),
    Column('End_Time', DateTime, primary_key=True),
    Column('District_ID', String(4), primary_key=True),
    Column('Weather_Type_ID', Integer, ForeignKey('weather_types.Weather_Type_ID')),
    Column('MaxTemperature', Integer),
    Column('MinTemperature', Integer),
    Column('Remarks', String(255)),
    ForeignKeyConstraint(['District_ID'], ['district.District_ID'])
)

# Create the table if it doesn't exist
metadata.create_all(engine)

session.execute(sqlalchemy.text("DELETE FROM weather_forecast"))
session.commit()
print("weather_forecast table cleared")

# Fetch and parse data from all URLs
tmp = []
for url in urls:
    data = fetch_data(url)
    if not data: print(f"Failed to fetch data from {url}")
    parsed_data = parse_data(data)
    if not parsed_data: print(f"Failed to parse data from {url}")
    tmp.extend(parsed_data)

# Insert data into the database
for entry in tmp:
    stmt = mysql_insert(weather_forecast).values(entry)
    stmt = stmt.on_duplicate_key_update(
        MaxTemperature=stmt.inserted.MaxTemperature,
        MinTemperature=stmt.inserted.MinTemperature,
        Remarks=stmt.inserted.Remarks
    )
    session.execute(stmt)
session.commit()
print("Weather forecast data inserted successfully")