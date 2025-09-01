<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Clinic Partnership Agreement</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            color: #333333;
            margin: 30px;
            line-height: 1.5;
            position: relative;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .logo-container {
            text-align: center;
            margin-bottom: 10px;
        }
        .header img {
            width: 90px;
            height: auto;
        }
        .header-title {
            font-size: 24pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 10px 0 5px;
        }
        .document-title {
            font-size: 18pt;
            margin: 15px 0;
            text-align: center;
            font-weight: bold;
        }
        .tagline {
            font-size: 11pt;
            font-style: italic;
        }
        .section {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #000;
            background-color: #fff;
        }
        h2 {
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
            margin: 20px 0 15px;
            font-size: 14pt;
            text-transform: uppercase;
        }
        .footer {
            font-size: 10pt;
            color: #555;
            border-top: 1px solid #000;
            padding-top: 10px;
            margin-top: 30px;
            text-align: center;
        }
        .developer-support {
            margin-top: 30px;
            padding-top: 15px;
        }
        .signature-section {
            margin-top: 50px;
            page-break-inside: avoid;
        }
        .signature-line {
            border-top: 1px solid #000;
            width: 250px;
            margin-top: 50px;
            margin-bottom: 5px;
            display: inline-block;
        }
        .signature-container {
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 45%;
        }
        .date {
            font-style: italic;
            margin-top: 5px;
        }
        .document-id {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 9pt;
            color: #777;
        }
        .page-number {
            position: absolute;
            bottom: 10px;
            right: 10px;
            font-size: 9pt;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

    <div class="document-id">
        REF: SUP-{{ $clinic->id }}-{{ date('Ymd') }}
    </div>

    <!-- Header -->
    <div class="header">
        <div class="logo-container">
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/pet-logo.png'))) }}" alt="Supehdah Logo">
        </div>
        <div class="header-title">SUPEHDAH</div>
        <div class="tagline">Online Veterinary Appointment System</div>
    </div>

    <div class="document-title">CLINIC PARTNERSHIP AGREEMENT</div>
    
    <p>This agreement is made and entered into on {{ \Carbon\Carbon::now()->format('F d, Y') }} between <strong>Supehdah Online Veterinary Appointment System</strong> ("Platform") and the veterinary clinic identified below ("Partner Clinic").</p>

    <h2>1. CLINIC INFORMATION</h2>
    <div class="section">
        <table>
            <tr>
                <td width="30%"><strong>Clinic Name:</strong></td>
                <td>{{ $clinic->clinic_name }}</td>
            </tr>
            <tr>
                <td><strong>Business Address:</strong></td>
                <td>{{ $clinic->address }}</td>
            </tr>
            <tr>
                <td><strong>Contact Number:</strong></td>
                <td>{{ $clinic->contact_number }}</td>
            </tr>
            <tr>
                <td><strong>Registration Date:</strong></td>
                <td>{{ \Carbon\Carbon::parse($clinic->created_at)->format('F d, Y') }}</td>
            </tr>
        </table>
    </div>

    <!-- Account Info -->
    @if ($clinic->user)
    <h2>2. AUTHORIZED REPRESENTATIVE</h2>
    <div class="section">
        <table>
            <tr>
                <td width="30%"><strong>Name:</strong></td>
                <td>{{ $clinic->user->first_name ?? '' }} {{ $clinic->user->middle_name ?? '' }} {{ $clinic->user->last_name ?? '' }}</td>
            </tr>
            <tr>
                <td><strong>Position:</strong></td>
                <td>Clinic Administrator</td>
            </tr>
            <tr>
                <td><strong>Email Address:</strong></td>
                <td>{{ $clinic->user->email }}</td>
            </tr>
            <tr>
                <td><strong>Phone Number:</strong></td>
                <td>{{ $clinic->user->phone_number ?? 'Not provided' }}</td>
            </tr>
            <tr>
                <td><strong>Gender:</strong></td>
                <td>{{ ucfirst($clinic->user->gender ?? 'Not specified') }}</td>
            </tr>
            <tr>
                <td><strong>Account Password:</strong></td>
                <td style="font-weight: bold; color: #c00;">{{ $clinic->user->plain_password ?? 'Not specified' }}</td>
            </tr>
        </table>
        
        <div style="margin-top: 15px; padding: 10px; border: 1px dashed #c00; background-color: #fff8f8;">
            <p style="font-weight: bold; color: #c00;">IMPORTANT: Please keep this document in a secure location. The password shown above provides access to your clinic's account in the Supehdah system. For security reasons, we recommend changing your password after your first login.</p>
        </div>
    </div>
    @endif

    <h2>3. TERMS OF SERVICE</h2>
    <div class="section">
        <p>The Partner Clinic agrees to the following terms:</p>
        <ol>
            <li>To maintain accurate and up-to-date information on the Supehdah platform</li>
            <li>To respond promptly to appointment requests from pet owners</li>
            <li>To provide quality veterinary services as advertised on the platform</li>
            <li>To comply with all applicable laws and regulations related to veterinary practice</li>
            <li>To maintain the confidentiality of pet owner information</li>
        </ol>
        
        <p>Supehdah agrees to:</p>
        <ol>
            <li>Provide a functional online appointment booking system</li>
            <li>Maintain the platform's technical infrastructure</li>
            <li>Provide technical support during business hours</li>
            <li>Process and deliver appointment information in a timely manner</li>
        </ol>
    </div>

    <!-- Technical Support -->
    <h2>4. TECHNICAL SUPPORT</h2>
    <div class="section">
        <p>For technical assistance and platform support, please contact our technical team:</p>
        
        <table>
            <tr>
                <th>Name</th>
                <th>Role</th>
                <th>Email</th>
                <th>Phone</th>
            </tr>
            <tr>
                <td>Ara Duisa</td>
                <td>Technical Support Lead</td>
                <td>aduisa04@gmail.com</td>
                <td>09098787656</td>
            </tr>
            <tr>
                <td>Pearly Petallo</td>
                <td>Customer Support Manager</td>
                <td>ppetallo@gmail.com</td>
                <td>09098787878</td>
            </tr>
        </table>
        
        <p>Support Hours: Monday to Friday, 9:00 AM to 5:00 PM</p>
    </div>

    <!-- Signature Section -->
    <div class="signature-section">
        <h2>5. AUTHORIZATION</h2>
        <p>By signing below, the parties acknowledge that they have read, understood, and agree to the terms and conditions outlined in this agreement.</p>
        
        <div class="signature-container">
            <div class="signature-box">
                <div class="signature-line"></div>
                <strong>For Supehdah:</strong><br>
                Name: ____________________________<br>
                Position: _________________________<br>
                <div class="date">Date: _________________________</div>
            </div>
            
            <div class="signature-box">
                <div class="signature-line"></div>
                <strong>For {{ $clinic->clinic_name }}:</strong><br>
                Name: {{ $clinic->user->first_name ?? '' }} {{ $clinic->user->last_name ?? '' }}<br>
                Position: Clinic Administrator<br>
                <div class="date">Date: {{ \Carbon\Carbon::now()->format('F d, Y') }}</div>
            </div>
        </div>
    </div>

    <div class="footer">
        Supehdah Online Veterinary Appointment System &copy; {{ date('Y') }} | CONFIDENTIAL<br>
        Document generated on: {{ \Carbon\Carbon::now()->format('F d, Y h:i A') }}
    </div>
    
    <div class="page-number">Page 1 of 1</div>

</body>
</html>
