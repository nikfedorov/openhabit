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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family:
                -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen,
                Ubuntu, sans-serif;
            background-color: var(--tg-theme-bg-color, #ffffff);
            color: var(--tg-theme-text-color, #000000);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .loading {
            text-align: center;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 3px solid var(--tg-theme-hint-color, #cccccc);
            border-top-color: var(--tg-theme-button-color, #3390ec);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 16px;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .error {
            text-align: center;
            padding: 20px;
        }

        .error-message {
            color: var(--tg-theme-destructive-text-color, #ff3b30);
        }

        .error-hint {
            margin-top: 10px;
            color: var(--tg-theme-hint-color, #999);
        }
    </style>
</head>
<body>
    <div id="loading" class="loading">
        <div class="spinner"></div>
        <p>Loading...</p>
    </div>

    <div id="error" class="error" style="display: none">
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
            fetch("{{ route('telegram-miniapp.auth') }}", {
                method: "POST",
                headers: {
                    "X-Telegram-Init-Data": initData,
                    Accept: "application/json",
                    "Content-Type": "application/json",
                },
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
