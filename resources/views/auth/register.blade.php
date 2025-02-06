<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css'])
    <style>
        html, body {
            height: 100%;
        }
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
        }
        .register-container {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            padding: 2rem;
        }
        .btn-custom {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-custom:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

    </style>
    <title>REGISTER</title>
</head>

<body>
    <div class="register-container">
        <h3 class="text-center mb-4">
            <a class="text-decoration-none text-dark" href="/">Finance App</a>
        </h3>
        <h2 class="text-center mb-4 text-primary"><b>REGISTER</b></h2>
        <form method="POST" action='/register'>
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label"><b>Name:</b></label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label"><b>Email:</b></label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label"><b>Password:</b></label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label"><b>Confirm Password:</b></label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>

            <div class="d-grid mb-4">
                <button type="submit" class="btn btn-custom text-white"><b>REGISTER</b></button>
            </div>

            <div class="text-center">
                <a href="/login" class="text-primary">Already have an account ? Login here</a>
            </div>
        </form>
    </div>
</body>

</html>
