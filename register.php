<?php
require 'config.php';

// Initialize variables to prevent undefined index warnings
$name = '';
$email = '';
$password = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Process form data when form is submitted
    $name = mysqli_real_escape_string($db_connection, $_POST['name']);
    $email = mysqli_real_escape_string($db_connection, $_POST['email']);
    $password = mysqli_real_escape_string($db_connection, $_POST['password']);

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user data into database
    $insert = mysqli_query($db_connection, "INSERT INTO `user` (`name`, `email`, `password`) VALUES ('$name', '$email', '$hashed_password')");

    if ($insert) {
        // Registration successful
        $_SESSION['login_id'] = mysqli_insert_id($db_connection); // Store user ID in session

        // Show success message using JavaScript
        echo '<script>alert("Registration successful!"); window.location = "login.php";</script>';
        exit;
    } else {
        // Registration failed
        echo "Registration failed! Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <link rel="shortcut icon" href="images/logow.png" type="image/x-icon">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Techtool Registration</title>
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            -webkit-box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 10px;
            margin: 0;
        }

        .header {
            justify-content: center;
            display: flex;
            margin-top: 100px;
            
        }

        .container {
            width: 100%;
            max-width: 600px;
        }
        label{
            display: block;
        }
        input {
            margin-bottom: 20px;
            margin-top: 5px;
        }

        .btn {
            width: 100%;
            max-width: 600px;
        }

        .regis {
            width: 100%;
            max-width: 600px;
        }

        .google {
            width: 100%;
            max-width: 600px;
        }
        .login{
            color: blue;
            text-decoration: underline;
        }
    </style>
</head>

<body class="bg-white dark:bg-gray-800">

    <section class="image" style="background-image: url('images/llleaves.svg');">
        <div class="header ">
            <div  class="container py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100  focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    <!-- Registration form -->
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                    <label for="">Name</label>
                        <input type="text" name="name" placeholder="Name" value="<?php echo htmlspecialchars($name); ?>"  class="pass bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Name" required />
                        <label for="">Email</label>
                        <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>"   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="example@company.com" required />
                        <label for="">Password</label>
                        <input type="password" name="password" placeholder="Password"  class="pass bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="•••••••••" required />
                        <button type="submit" name="register"  class="regis py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Register</button>
                        <p>Already have an account? <a href="login.php"><span class="login">Login here</span></a></p>
                    </form>
                    
                </div>
                </div>
            </div>
        </div>
    </section>

</body>
</html>