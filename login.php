<?php
require 'config.php';
if (isset($_SESSION['login_id'])) {
    header('Location: index.php');
    exit;
}
require __DIR__ . '/vendor/autoload.php';

// Google client setup
$client = new Google_Client();
$client->setClientId('1056219329736-480atnc64jat8cgndf9mg42fa00i23d6.apps.googleusercontent.com');
$client->setClientSecret('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');
$redirectUri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");

// Google login flow
if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    if (!isset($token["error"])) {
        $client->setAccessToken($token['access_token']);
        $google_oauth = new Google_Service_Oauth2($client);
        $google_account_info = $google_oauth->userinfo->get();

        // Storing data into database if user doesn't exist
        $id = mysqli_real_escape_string($db_connection, $google_account_info->id);
        $full_name = mysqli_real_escape_string($db_connection, trim($google_account_info->name));
        $email = mysqli_real_escape_string($db_connection, $google_account_info->email);
        $profile_pic = mysqli_real_escape_string($db_connection, $google_account_info->picture);

        // Check if user exists
        $get_user = mysqli_query($db_connection, "SELECT `google_id` FROM `user` WHERE `google_id`='$id'");
        if (mysqli_num_rows($get_user) > 0) {
            $_SESSION['login_id'] = $id;
            header('Location: index.php');
            exit;
        } else {
            // Insert user if not exists
            $insert = mysqli_query($db_connection, "INSERT INTO `user`(`google_id`,`name`,`email`,`profile_image`) VALUES('$id','$full_name','$email','$profile_pic')");
            if ($insert) {
                $_SESSION['login_id'] = $id;
                header('Location: index.php');
                exit;
            } else {
                echo "Sign up failed! (Something went wrong)";
            }
        }
    } else {
        header('Location: login.php');
        exit;
    }
} else {
    // Regular login flow
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Handle user login
        if (isset($_POST['login'])) {
            $email = mysqli_real_escape_string($db_connection, $_POST['email']);
            $password = mysqli_real_escape_string($db_connection, $_POST['password']);

            // Fetch user details
            $get_user = mysqli_query($db_connection, "SELECT * FROM `users` WHERE `email`='$email'");
            if (mysqli_num_rows($get_user) > 0) {
                $user = mysqli_fetch_assoc($get_user);
                if (password_verify($password, $user['password'])) {
                    $_SESSION['login_id'] = $user['google_id']; // Assuming `google_id` is your unique user identifier
                    header('Location: index.php');
                    exit;
                } else {
                    echo "Invalid password.";
                }
            } else {
                echo "User not found.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="shortcut icon" href="images/logow.png" type="image/x-icon">

    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Techtool Login</title>
    <style>
        /* Reset and Global Styles */
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
            align-items: center;
            height: 90vh;
        }


        .container {
            width: 100%;
            max-width: 600px;

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
        .header-image{
            text-align: center;
            width: 100%;
            max-width: 600px;
        }
        .image #lottie-animation{
            width: 100%;
            max-width: 600px;
        }
    </style>
</head>

<body class="bg-white dark:bg-gray-800">
    <section class="image" style="background-image: url('images/llleaves.svg');">
        <div class="header">
            <div class="header-image">
            <h1 class="mb-8 text-4xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-6xl dark:text-white">HR<span class="text-blue-600 dark:text-blue-500"> Vault</span></h1>
            <p class="text-lg font-normal text-gray-500 lg:text-xl dark:text-gray-400">Here at Flowbite we focus on markets where technology, innovation, and capital can unlock long term value and drive economic growth.</p>
                <div id="lottie-animation"></div>
            </div>
            <div
                class="container py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100  focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" type="email" id="email"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="example@company.com" required />
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password"
                            class="pass bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="•••••••••" required />
                    </div>
                    <div class="form-group ">

                        <button type="submit" name="login" type="button"
                            class="btn text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Login</button>
                    </div>
                </form>

                <a class="login-with-google-btn" href="<?php echo $client->createAuthUrl(); ?>">
                    <button
                        class="regis py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                        <i class="fa-brands fa-google"></i> Sign in with google</button>
                </a>
                <a href="register.php">
                    <button type="button"
                        class="regis py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Register</button>
                </a>
            </div>
        </div>
        </div>
    </section>
  
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.7.6/lottie.min.js"></script>
    <script src="script/indexs.js"></script>
    <script>
        var animation = lottie.loadAnimation({
            container: document.getElementById('lottie-animation'),
            renderer: 'svg',
            loop: true,
            autoplay: true,
            path: './image/docu.json'
        });
    </script>
</body>

</html>