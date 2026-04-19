<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"
    />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ config('app.name') }}</title>
    <script src="https://telegram.org/js/telegram-web-app.js"></script>
    <script>
        (function() {
            var theme = localStorage.getItem('theme');
            var isDark = theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches);
            if (isDark) document.documentElement.classList.add('dark');
        })();
    </script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html { background: #fff; }
        html.dark { background: #171717; }

        body {
            font-family:
                -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen,
                Ubuntu, sans-serif;
            background-color: inherit;
            color: #000;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding-bottom: 20vh;
        }

        html.dark body {
            color: #fff;
        }

        .loading {
            text-align: center;
        }

        #splash-bg { fill: #f5f5f5; }
        html.dark #splash-bg { fill: #171717; }

        .error {
            text-align: center;
            padding: 20px;
            display: none;
        }

        .error-message {
            color: #ff3b30;
        }

        .error-hint {
            margin-top: 10px;
            color: #999;
        }
    </style>
</head>
<body>
    <div id="loading" class="loading">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="none" width="64" height="64"><rect id="splash-bg" width="512" height="512" rx="96" fill="#f5f5f5"/><rect x="80" y="272" width="160" height="160" rx="24" fill="#14532d"><animate attributeName="opacity" values="0.4;1;0.4" dur="1.6s" begin="0s" repeatCount="indefinite"/></rect><rect x="272" y="272" width="160" height="160" rx="24" fill="#15803d"><animate attributeName="opacity" values="0.4;1;0.4" dur="1.6s" begin="0.2s" repeatCount="indefinite"/></rect><rect x="80" y="80" width="160" height="160" rx="24" fill="#16a34a"><animate attributeName="opacity" values="0.4;1;0.4" dur="1.6s" begin="0.4s" repeatCount="indefinite"/></rect><rect x="272" y="80" width="160" height="160" rx="24" fill="#22c55e"><animate attributeName="opacity" values="0.4;1;0.4" dur="1.6s" begin="0.6s" repeatCount="indefinite"/></rect></svg>
    </div>

    <div id="error" class="error">
        <p class="error-message" id="error-message"></p>
        <p class="error-hint">Please try again or contact support.</p>
    </div>

    <script>
        Telegram.WebApp.ready();
        Telegram.WebApp.expand();

        const initData = Telegram.WebApp.initData;

        function showError(message) {
            document.getElementById("loading").style.display = "none";
            document.getElementById("error").style.display = "block";
            document.getElementById("error-message").textContent = message;
        }

        if (initData) {
            fetch("{{ route('api.auth.telegram') }}", {
                method: "POST",
                headers: {
                    Accept: "application/json",
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({ init_data: initData }),
                credentials: "include",
            })
                .then((response) =>
                    response.json().then((data) => ({ ok: response.ok, data })),
                )
                .then(({ ok, data }) => {
                    if (!ok) {
                        throw new Error(data.error || "Authentication failed");
                    }
                    localStorage.setItem('api_token', data.token);
                    window.location.href = '/app';
                })
                .catch((error) => {
                    showError(error.message);
                });
        } else {
            showError("This app must be opened from Telegram.");
        }
    </script>
</body>
</html>
