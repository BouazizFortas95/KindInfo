<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate of Completion</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .certificate {
            max-width: 800px;
            margin: 20px auto;
            padding: 40px;
            background: white;
            border: 15px solid #gold;
            position: relative;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .certificate::before {
            content: "";
            position: absolute;
            top: 20px;
            left: 20px;
            right: 20px;
            bottom: 20px;
            border: 2px solid #ddd;
            pointer-events: none;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            width: 100px;
            height: 100px;
            margin: 0 auto 20px;
            background: #eee;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
        }
        h1 {
            color: #333;
            font-size: 36px;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 3px;
        }
        .subtitle {
            color: #666;
            font-size: 18px;
            margin: 10px 0;
        }
        .recipient {
            text-align: center;
            margin: 40px 0;
        }
        .presented-to {
            font-size: 16px;
            color: #666;
            margin-bottom: 10px;
        }
        .recipient-name {
            font-size: 32px;
            font-weight: bold;
            color: #333;
            margin: 10px 0;
            text-transform: capitalize;
        }
        .award {
            text-align: center;
            margin: 30px 0;
            font-size: 18px;
            line-height: 1.6;
        }
        .course-title {
            font-weight: bold;
            color: #333;
        }
        .details {
            display: flex;
            justify-content: space-between;
            margin: 40px 0;
            padding: 0 20px;
        }
        .detail-item {
            text-align: center;
        }
        .detail-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }
        .detail-value {
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }
        .signature {
            display: flex;
            justify-content: space-around;
            margin: 50px 0 20px;
            padding: 0 20px;
        }
        .signature-block {
            text-align: center;
            width: 200px;
        }
        .signature-line {
            height: 1px;
            background: #333;
            margin: 40px 0 10px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="header">
            <div class="logo">🎓</div>
            <h1>{{ $certificate->translateOrDefault(app()->getLocale())->title ?? 'Certificate of Completion' }}</h1>
            <div class="subtitle">This certificate is proudly presented to</div>
        </div>
        
        <div class="recipient">
            <div class="presented-to">Presented to</div>
            <div class="recipient-name">{{ $certificate->user->name }}</div>
        </div>
        
        <div class="award">
            {{ $certificate->translateOrDefault(app()->getLocale())->body ?? 'For successfully completing the course requirements' }}
            <br><br>
            <strong class="course-title">{{ $certificate->course->translateOrDefault(app()->getLocale())->title ?? 'Course' }}</strong>
        </div>
        
        <div class="details">
            <div class="detail-item">
                <div class="detail-label">Certificate ID</div>
                <div class="detail-value">{{ $certificate->certificate_uuid }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Date Issued</div>
                <div class="detail-value">{{ $certificate->issued_at->format('F j, Y') }}</div>
            </div>
        </div>
        
        <div class="signature">
            <div class="signature-block">
                <div class="signature-line"></div>
                <div>Authorized Signature</div>
            </div>
        </div>
        
        <div class="footer">
            KindInfo Learning Platform<br>
            Valid Certificate
        </div>
    </div>
</body>
</html>