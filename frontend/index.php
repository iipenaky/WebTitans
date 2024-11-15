<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant - Welcome</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/style.css">
    <script defer src="./script/index.js"></script>
</head>
<body class="bg-gray-100 text-gray-900">
  <header class="bg-black p-4">
    <div class="container mx-auto flex justify-between items-center">
      <img src="Untitled design.png" alt="Logo" class="logo w-50 h-50"> 
      
      <nav>
        <ul class="flex space-x-6 text-white">
          <li><a href="index.html" class="font-bold hover:text-red-600 transition duration-300">Home</a></li>
          <li><a href="about.html" class="font-bold hover:text-red-600 transition duration-300">About</a></li>
          <li><a href="menu.html" class="font-bold hover:text-red-600 transition duration-300">Menu</a></li>
          <li><a href="team.html" class="font-bold hover:text-red-600 transition duration-300">Team</a></li>
          <!-- Sign up when not logged in, and logout when logged in-->
          <li><a href="signup.html" class="font-bold hover:text-red-600 transition duration-300">Sign Up</a></li>
        </ul>
      </nav>
    </div>
  </header>
  <section class="relative h-screen">
    <!--Change video-->
    <video autoplay muted loop class="absolute inset-0 w-full h-full object-cover" playsinline>
      <source src="./assets/video.MP4" type="video/mp4">
      Your browser does not support the video tag.
    </video>
    <div class="absolute inset-0 bg-black bg-opacity-50 flex flex-col items-center justify-center text-white">
      <h2 class="text-4xl font-bold mb-4 text-center">Welcome to Darryl's Restaurant</h2>
      <p class="text-lg text-center max-w-2xl mb-8">
        Welcome to Darryl's Restaurant, where our passion for exceptional cuisine and unforgettable dining experiences come together.
        From our carefully curated ingredients to our expertly crafted dishes, we invite you to discover flavors that reflect the rich tapestry 
        of culinary artistry. Whether you're here to savor a delicious meal, celebrate a special occasion, or simply relax and enjoy, 
        we are dedicated to making your visit a memorable one.
      </p>
      <a href="menu.html" class="bg-black text-white px-6 py-3 rounded hover:bg-red-600 transition duration-300">Order Now</a>
    </div>
  </section>

  <footer class="bg-black text-white py-8 text-center">
    <p><a href="mailto:info@darrylrestaurant.com" class="text-gray-400 hover:text-red-600 transition duration-300">info@darrylsrestaurant.com</a></p>
    <p>Location: 123 Food Street, Cantonments</p>
  </footer>

</body>
</html>
