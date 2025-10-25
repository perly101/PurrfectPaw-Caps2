<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Welcome to PurrfectPaw</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f8fafc;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Cg fill-rule='evenodd'%3E%3Cg fill='%239C92AC' fill-opacity='0.05'%3E%3Cpath opacity='.5' d='M96 95h4v1h-4v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9zm-1 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-9-10h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm9-10v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-9-10h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm9-10v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-9-10h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm9-10v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-9-10h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9z'/%3E%3Cpath d='M6 5V0H5v5H0v1h5v94h1V6h94V5H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
      background-attachment: fixed;
    }
    .card-shadow {
      box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    .gradient-btn {
      background: linear-gradient(135deg, #6366F1, #8B5CF6);
      transition: all 0.3s ease;
    }
    .gradient-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 15px -3px rgba(107, 70, 193, 0.3);
    }
    .accent-border {
      position: relative;
    }
    .accent-border:after {
      content: '';
      position: absolute;
      left: 0;
      top: 10%;
      height: 80%;
      width: 6px;
      background: linear-gradient(to bottom, #6366F1, #8B5CF6);
      border-radius: 3px;
    }
  </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 md:p-6 text-gray-800">
  <div class="max-w-5xl w-full">
    <div class="flex flex-col lg:flex-row rounded-3xl overflow-hidden card-shadow bg-white">
      <!-- Left column - Image -->
      <div class="lg:w-1/2 h-64 lg:h-auto relative overflow-hidden">
        <img src="/images/welcome3.jpg" alt="Pets" class="absolute inset-0 w-full h-full object-cover" />
        <div class="absolute inset-0 bg-gradient-to-t from-indigo-900/60 to-purple-600/30 flex flex-col justify-end p-8">
          <div class="text-white">
            <h2 class="text-3xl font-bold mb-2">Caring for pets is our passion</h2>
            <p class="text-white/80 text-sm mb-4">Streamlined veterinary management for happier pets and practices</p>
          </div>
        </div>
      </div>
      
      <!-- Right column - Content -->
      <div class="lg:w-1/2 p-8 md:p-12 accent-border">
        <div class="flex flex-col h-full">
          <!-- Logo and heading -->
          <div class="mb-8 flex items-center">
            <img src="/images/newlogo.png" alt="PurrfectPaw Logo" class="w-14 h-14 rounded-full shadow-md mr-4" />
            <div>
              <h1 class="text-3xl font-bold text-gray-800">
                Purrfect<span class="text-indigo-600">Paw</span>
              </h1>
              <p class="text-sm text-gray-500">Veterinary Management System</p>
            </div>
          </div>
          
          <!-- Welcome content -->
          <div class="flex-grow">
            <h2 class="text-2xl font-bold text-gray-700 mb-4">Welcome!</h2>
            <p class="text-gray-600 mb-6 leading-relaxed">
              Experience seamless veterinary practice management with our comprehensive platform. 
              Schedule appointments, manage patient records, and streamline your workflow all in one place.
            </p>
            
            <!-- Features -->
            <div class="grid grid-cols-2 gap-4 mb-8">
              <div class="flex items-center">
                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center mr-3">
                  <i class="fas fa-calendar-check text-indigo-600"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Easy Scheduling</span>
              </div>
              <div class="flex items-center">
                <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                  <i class="fas fa-user-md text-purple-600"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Patient Records</span>
              </div>
              <div class="flex items-center">
                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center mr-3">
                  <i class="fas fa-bell text-indigo-600"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Reminders</span>
              </div>
              <div class="flex items-center">
                <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                  <i class="fas fa-chart-line text-purple-600"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Analytics</span>
              </div>
            </div>
          </div>
          
          <!-- CTA Buttons -->
          <div class="flex flex-col sm:flex-row gap-4">
            <a href="/login" class="gradient-btn text-white px-8 py-3 rounded-lg text-center font-medium flex items-center justify-center">
              <i class="fas fa-sign-in-alt mr-2"></i> Sign In
            </a>
            <a href="/register" class="bg-white border-2 border-indigo-200 text-indigo-600 px-8 py-3 rounded-lg text-center font-medium hover:border-indigo-300 hover:bg-indigo-50 transition-all duration-200 flex items-center justify-center">
              <i class="fas fa-user-plus mr-2"></i> Create Account
            </a>
          </div>
        </div>
        
        <!-- Footer -->
        <div class="mt-8 text-center text-sm text-gray-400">
          &copy; {{ date('Y') }} PurrfectPaw. All rights reserved.
        </div>
      </div>
    </div>
    
    <!-- System Status -->
    <div class="mt-4 text-center">
      <span class="inline-flex items-center text-xs text-green-600">
        <span class="w-2 h-2 rounded-full bg-green-500 mr-1"></span> System online
      </span>
    </div>
  </div>
</body>
</html>
