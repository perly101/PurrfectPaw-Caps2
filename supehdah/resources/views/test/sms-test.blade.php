<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMS Test - AutoRepaircdaw Clinic</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button {
            background: #007bff;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 10px;
        }
        button:hover {
            background: #0056b3;
        }
        .result {
            margin-top: 20px;
            padding: 15px;
            border-radius: 5px;
            display: none;
        }
        .success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .test-section {
            margin-bottom: 40px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        h2 {
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>SMS Testing - AutoRepaircdaw Clinic</h1>
        <p>Use this page to test SMS functionality using Semaphore API.</p>

        <!-- Basic SMS Test -->
        <div class="test-section">
            <h2>1. Basic SMS Test</h2>
            <div class="form-group">
                <label for="phone1">Phone Number:</label>
                <input type="tel" id="phone1" placeholder="e.g. 09123456789" value="09123456789">
            </div>
            <div class="form-group">
                <label for="message1">Message:</label>
                <textarea id="message1" rows="3" placeholder="Enter test message">This is a test SMS from AutoRepaircdaw clinic system.</textarea>
            </div>
            <button onclick="testBasicSms()">Send Basic SMS</button>
            <div id="result1" class="result"></div>
        </div>

        <!-- Appointment Confirmation Test -->
        <div class="test-section">
            <h2>2. Appointment Confirmation SMS Test</h2>
            <div class="form-group">
                <label for="phone2">Phone Number:</label>
                <input type="tel" id="phone2" placeholder="e.g. 09123456789" value="09123456789">
            </div>
            <div class="form-group">
                <label for="patient_name">Patient Name:</label>
                <input type="text" id="patient_name" placeholder="e.g. John Doe" value="John Doe">
            </div>
            <div class="form-group">
                <label for="doctor_name">Doctor Name:</label>
                <input type="text" id="doctor_name" placeholder="e.g. Dr. Smith" value="Dr. Smith">
            </div>
            <div class="form-group">
                <label for="date">Appointment Date:</label>
                <input type="text" id="date" placeholder="e.g. Dec 25, 2024" value="Dec 25, 2024">
            </div>
            <div class="form-group">
                <label for="time">Appointment Time:</label>
                <input type="text" id="time" placeholder="e.g. 2:00 PM" value="2:00 PM">
            </div>
            <button onclick="testAppointmentConfirmation()">Send Appointment Confirmation</button>
            <div id="result2" class="result"></div>
        </div>

        <div class="test-section">
            <h2>Notes</h2>
            <ul>
                <li><strong>API Key:</strong> 6dff29a20c4ad21b0ff30725e15c23d0</li>
                <li><strong>Sender Name:</strong> AutoRepaircdaw</li>
                <li><strong>Phone Format:</strong> Will be automatically formatted for Philippines (+63)</li>
                <li>Check browser console and Laravel logs for detailed information</li>
                <li>Make sure your phone number is valid to receive test SMS</li>
            </ul>
        </div>
    </div>

    <script>
        function showResult(resultId, success, message, data = null) {
            const resultDiv = document.getElementById(resultId);
            resultDiv.className = 'result ' + (success ? 'success' : 'error');
            resultDiv.innerHTML = '<strong>' + (success ? 'Success!' : 'Error!') + '</strong><br>' + message;
            if (data) {
                resultDiv.innerHTML += '<br><br><strong>Details:</strong><br>' + JSON.stringify(data, null, 2);
            }
            resultDiv.style.display = 'block';
        }

        async function testBasicSms() {
            const phone = document.getElementById('phone1').value;
            const message = document.getElementById('message1').value;
            
            if (!phone || !message) {
                showResult('result1', false, 'Please fill in all fields');
                return;
            }

            try {
                const response = await fetch(`/test/sms?phone=${encodeURIComponent(phone)}&message=${encodeURIComponent(message)}`);
                const data = await response.json();
                
                showResult('result1', data.success, data.message, data);
            } catch (error) {
                showResult('result1', false, 'Network error: ' + error.message);
            }
        }

        async function testAppointmentConfirmation() {
            const phone = document.getElementById('phone2').value;
            const patientName = document.getElementById('patient_name').value;
            const doctorName = document.getElementById('doctor_name').value;
            const date = document.getElementById('date').value;
            const time = document.getElementById('time').value;
            
            if (!phone || !patientName || !doctorName || !date || !time) {
                showResult('result2', false, 'Please fill in all fields');
                return;
            }

            try {
                const params = new URLSearchParams({
                    phone: phone,
                    patient_name: patientName,
                    doctor_name: doctorName,
                    date: date,
                    time: time
                });

                const response = await fetch(`/test/sms/appointment-confirmation?${params}`);
                const data = await response.json();
                
                showResult('result2', data.success, data.message, data);
            } catch (error) {
                showResult('result2', false, 'Network error: ' + error.message);
            }
        }
    </script>
</body>
</html>