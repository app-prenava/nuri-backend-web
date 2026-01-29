<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Service Prenava</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- CSS --}}
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            height: 100vh;
            background: linear-gradient(270deg, #6a00ff, #ff008c, #00f7ff);
            background-size: 600% 600%;
            animation: gradientMove 6s ease infinite;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        @keyframes gradientMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .container {
            text-align: center;
            animation: beat 1s infinite;
        }

        h1 {
            font-size: 3.5rem;
            color: #fff;
            text-shadow:
                0 0 10px #fff,
                0 0 20px #ff00ff,
                0 0 40px #00ffff;
            animation: glow 1.5s ease-in-out infinite alternate;
        }

        @keyframes glow {
            from {
                text-shadow:
                    0 0 10px #fff,
                    0 0 20px #ff00ff,
                    0 0 30px #00ffff;
            }
            to {
                text-shadow:
                    0 0 20px #fff,
                    0 0 40px #ff00ff,
                    0 0 80px #00ffff;
            }
        }

        @keyframes beat {
            0%   { transform: scale(1); }
            50%  { transform: scale(1.08); }
            100% { transform: scale(1); }
        }

        .pulse {
            position: absolute;
            width: 300px;
            height: 300px;
            background: rgba(255,255,255,0.15);
            border-radius: 50%;
            animation: pulseAnim 2s infinite;
        }

        @keyframes pulseAnim {
            0% {
                transform: scale(0.8);
                opacity: 0.8;
            }
            100% {
                transform: scale(2);
                opacity: 0;
            }
        }
    </style>
</head>
<body>

    <div class="pulse"></div>

    <div class="container" id="beatBox">
        <h1>Welcome to Service Prenava</h1>
    </div>

    {{-- JS --}}
    <script>
        const box = document.getElementById('beatBox');

        let scaleUp = true;

        setInterval(() => {
            box.style.transform = scaleUp ? 'scale(1.12)' : 'scale(1)';
            scaleUp = !scaleUp;
        }, 450);
    </script>

</body>
</html>
