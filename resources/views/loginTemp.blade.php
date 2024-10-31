<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .login-container {
            max-width: 400px;
            margin: 50px auto;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2 class="text-center">Login</h2>
        <div id="error-message" class="error"></div>
        <div class="form-group mb-3">
            <label for="email">Email</label>
            <input type="email" id="email" class="form-control" placeholder="Enter your email" required>
        </div>
        <div class="form-group mb-3">
            <label for="password">Password</label>
            <input type="password" id="password" class="form-control" placeholder="Enter your password" required>
        </div>
        <button class="btn btn-primary w-100" onclick="login()">Log In</button>
    </div>

    <script>
        async function login() {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const errorMessage = document.getElementById('error-message');

            // Clear any previous error message
            errorMessage.textContent = "";

            // Check if email includes "@"
            if (!email.includes("@")) {
                errorMessage.textContent = "The email address must contain '@'.";
                return;
            }

            try {
                // Fetch the users JSON file
                const response = await fetch('/json/users.json'); // Update to the correct path
                if (!response.ok) {
                    throw new Error("HTTP error " + response.status);
                }

                const data = await response.json();
                const users = data.users;

                const user = users.find(user => user.email === email && user.password === password);

                if (!user) {
                    errorMessage.textContent = "Invalid email or password";
                    return;
                }

                // Redirect based on user role
                if (user.role === "admin") {
                    window.location.href = "{{ url('/admin/homeAdmin') }}";
                } else if (user.role === "professor") {
                    window.location.href = "{{ url('/professor/homeProfessor') }}";
                } else if (user.role === "staff") {
                    window.location.href = "{{ url('/staff/homeStaff') }}";
                } else {
                    alert("ACCESS DENIED");
                    window.location.href = "{{ url('home') }}";
                }

            } catch (error) {
                errorMessage.textContent = "Error fetching users data: " + error.message;
            }
        }
    </script>
</body>
</html>
