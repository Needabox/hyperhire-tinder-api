<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Popular People Alert</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
            border-top: none;
        }
        .person-card {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .person-name {
            font-size: 18px;
            font-weight: bold;
            color: #4CAF50;
            margin-bottom: 10px;
        }
        .person-info {
            margin: 5px 0;
        }
        .likes-count {
            background-color: #ff9800;
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
            display: inline-block;
            font-weight: bold;
            margin-top: 10px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔥 Popular People Alert</h1>
    </div>
    
    <div class="content">
        <p>Hello Admin,</p>
        
        <p>The following {{ count($popularPeople) }} {{ count($popularPeople) === 1 ? 'person has' : 'people have' }} received more than 50 likes:</p>
        
        @foreach($popularPeople as $person)
        <div class="person-card">
            <div class="person-name">{{ $person->name }}</div>
            <div class="person-info"><strong>ID:</strong> {{ $person->id }}</div>
            @if($person->age)
            <div class="person-info"><strong>Age:</strong> {{ $person->age }}</div>
            @endif
            <div class="person-info"><strong>Total Likes:</strong> <span class="likes-count">{{ $person->total_likes }}</span></div>
        </div>
        @endforeach
        
        <p>Please review these popular profiles.</p>
    </div>
    
    <div class="footer">
        <p>This is an automated email from Hyperhire Tinder API.</p>
        <p>Generated at: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>

