<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Admin Dashboard</title>
        <link rel="stylesheet" href="admin/styles.css">
    
        <style>
            #map { height: 600px; }
            #info { margin-top: 20px; padding: 10px; border: 1px solid #ccc; }
            #gameCanvas { border: 1px solid #000000; }
            #startButton { margin-top: 20px; padding: 10px; }
        </style>
    </head>
    <body>
        <header>
        <h1>Admin Dashboard</h1>
        <nav>
            <ul>
            <li><a href="manage_locations.php">管理景點</a></li>
            <li><a href="manage_users.php">管理使用者</a></li>
            <li><a href="manage_trails.php">管理步道</a></li>
            <li><a href="manage_departments.php">管理部門</a></li>
            <!-- 你可以在这里添加更多的管理页面链接 -->
        </ul>
        </nav>
        </header>
        <canvas id="gameCanvas" width="1200" height="600"></canvas>
        <button id="startButton">Start Game</button>

        <script>
            var canvas = document.getElementById("gameCanvas");
            var ctx = canvas.getContext("2d");

            const COLS = 40;
            const ROWS = 20;
            const BLOCK_SIZE = 30;

            function drawBlock(x, y, color) {
                ctx.fillStyle = color;
                ctx.fillRect(x * BLOCK_SIZE, y * BLOCK_SIZE, BLOCK_SIZE, BLOCK_SIZE);
                ctx.strokeRect(x * BLOCK_SIZE, y * BLOCK_SIZE, BLOCK_SIZE, BLOCK_SIZE);
            }

            const shapes = [
                [[1, 1, 1, 1]],  // I
                [[1, 1, 1], [0, 1, 0]],  // T
                [[1, 1, 0], [0, 1, 1]],  // Z
                [[0, 1, 1], [1, 1, 0]],  // S
                [[1, 1], [1, 1]],  // O
                [[1, 1, 1], [1, 0, 0]],  // L
                [[1, 1, 1], [0, 0, 1]]   // J
            ];

            const colors = ['cyan', 'purple', 'red', 'green', 'yellow', 'orange', 'blue'];

            let board = Array.from({ length: ROWS }, () => Array(COLS).fill(0));

            function drawBoard() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                for (let y = 0; y < ROWS; y++) {
                    for (let x = 0; x < COLS; x++) {
                        if (board[y][x]) {
                            drawBlock(x, y, board[y][x]);
                        }
                    }
                }
            }

            function createPiece() {
                const typeId = Math.floor(Math.random() * shapes.length);
                const shape = shapes[typeId];
                return {
                    x: Math.floor(COLS / 2) - Math.floor(shape[0].length / 2),
                    y: 0,
                    shape: shape,
                    color: colors[typeId]
                };
            }

            let piece = createPiece();

            function drawPiece(piece) {
                piece.shape.forEach((row, y) => {
                    row.forEach((value, x) => {
                        if (value) {
                            drawBlock(piece.x + x, piece.y + y, piece.color);
                        }
                    });
                });
            }

            function movePiece(dir) {
                piece.x += dir;
                if (collide()) {
                    piece.x -= dir;
                }
            }

            function dropPiece() {
                piece.y++;
                if (collide()) {
                    piece.y--;
                    freeze();
                    piece = createPiece();
                }
            }

            function rotatePiece() {
                const shape = piece.shape.map((_, index) =>
                    piece.shape.map(col => col[index])
                );
                piece.shape = shape.reverse();
                if (collide()) {
                    piece.shape = shape.reverse().map((_, index) =>
                        shape.map(row => row[index])
                    );
                }
            }

            function collide() {
                for (let y = 0; y < piece.shape.length; y++) {
                    for (let x = 0; x < piece.shape[y].length; x++) {
                        if (
                            piece.shape[y][x] &&
                            (board[piece.y + y] && board[piece.y + y][piece.x + x]) !== 0
                        ) {
                            return true;
                        }
                    }
                }
                return false;
            }

            function freeze() {
                piece.shape.forEach((row, y) => {
                    row.forEach((value, x) => {
                        if (value) {
                            board[piece.y + y][piece.x + x] = piece.color;
                        }
                    });
                });
                clearLines();
            }

            function clearLines() {
                board = board.reduce((acc, row) => {
                    if (row.every(cell => cell !== 0)) {
                        acc.unshift(Array(COLS).fill(0));
                    } else {
                        acc.push(row);
                    }
                    return acc;
                }, []);
            }

            document.addEventListener('keydown', event => {
                if (event.code === 'ArrowLeft') {
                    movePiece(-1);
                } else if (event.code === 'ArrowRight') {
                    movePiece(1);
                } else if (event.code === 'ArrowDown') {
                    dropPiece();
                } else if (event.code === 'ArrowUp') {
                    rotatePiece();
                }
            });

            let gameInterval;
            function startGame() {
                piece = createPiece();
                board = Array.from({ length: ROWS }, () => Array(COLS).fill(0));
                if (gameInterval) {
                    clearInterval(gameInterval);
                }
                gameInterval = setInterval(() => {
                    dropPiece();
                    drawBoard();
                    drawPiece(piece);
                }, 250);
            }

            document.getElementById('startButton').addEventListener('click', startGame);

            drawBoard();
        </script>
    </body>
</html>