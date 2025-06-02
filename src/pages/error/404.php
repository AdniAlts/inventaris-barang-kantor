<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Page Not Found | 404</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
    rel="stylesheet" />
</head>

<body
  class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center font-['Inter']">
  <div class="max-w-md mx-auto text-center p-8">
    <!-- Animated 404 text -->
    <div class="text-9xl font-bold text-indigo-600 mb-4 animate-bounce">
      4<span class="text-indigo-400">0</span>4
    </div>

    <!-- Error message -->
    <h1 class="text-3xl font-semibold text-gray-800 mb-2">
      Oops! Page not found
    </h1>
    <p class="text-gray-600 mb-8">
      The page you're looking for doesn't exist or has been moved.
    </p>

    <!-- Illustration -->
    <div class="mb-8">
      <svg
        class="w-64 h-64 mx-auto"
        viewBox="0 0 200 200"
        xmlns="http://www.w3.org/2000/svg">
        <circle
          cx="100"
          cy="100"
          r="80"
          fill="none"
          stroke="#818CF8"
          stroke-width="8"
          stroke-dasharray="5,5" />

        <circle cx="80" cy="80" r="8" fill="#6366F1" />
        <circle cx="120" cy="80" r="8" fill="#6366F1" />
        <path
          d="M70,130 Q100,150 130,130"
          fill="none"
          stroke="#6366F1"
          stroke-width="4"
          stroke-linecap="round" />
      </svg>
    </div>

    <!-- Action button -->
    <a
      href="<?php echo $loc ?>"
      class="inline-block px-6 py-3 bg-indigo-600 text-white font-medium rounded-lg shadow-md hover:bg-indigo-700 transition duration-300 ease-in-out transform hover:-translate-y-1">
      Go Back Home
    </a>

    <!-- Additional help -->
    <p class="mt-6 text-sm text-gray-500">
      Need help?
      <a href="#" class="text-indigo-600 hover:underline">Contact support</a>
    </p>
  </div>
</body>

</html>