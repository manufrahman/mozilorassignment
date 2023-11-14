<?php

// Function to block rendering based on selected options
function blockRendering($htmlContent, $selectedOptions, $customPattern)
{
    // Block Google Ads
    if (in_array('googleAds', $selectedOptions)) {
        $htmlContent = preg_replace('/<ins class="adsbygoogle".*<\/ins>/s', '', $htmlContent);
    }

    // Block Google Analytics
    if (in_array('googleAnalytics', $selectedOptions)) {
        $htmlContent = preg_replace('/<script.*google-analytics\.com\/analytics\.js.*<\/script>/s', '', $htmlContent);
    }

    // Block SoundCloud
    if (in_array('soundCloud', $selectedOptions)) {
        $htmlContent = preg_replace('/<script.*connect\.soundcloud\.com\/sdk.*<\/script>/s', '', $htmlContent);
    }

// // Block YouTube Embed
// if (in_array('youtubeEmbed', $selectedOptions)) {
//     $htmlContent = preg_replace('/<iframe.*?src="https:\/\/www\.youtube\.com\/embed\/([^"\']+)".*?<\/iframe>/i', '', $htmlContent);
// }

// Block YouTube Embed
if (in_array('youtubeEmbed', $selectedOptions)) {
    $doc = new DOMDocument();
    libxml_use_internal_errors(true);
    $doc->loadHTML($htmlContent);
    libxml_clear_errors();

    $iframes = $doc->getElementsByTagName('iframe');

    foreach ($iframes as $iframe) {
        $src = $iframe->getAttribute('src');

        // Check if the iframe source contains youtube.com/embed
        if (strpos($src, 'youtube.com/embed') !== false) {
            $iframe->parentNode->removeChild($iframe);
        }
    }

    $htmlContent = $doc->saveHTML();
}


    // Block custom pattern
    if (!empty($customPattern)) {
        $htmlContent = preg_replace('/' . preg_quote($customPattern, '/') . '/', '', $htmlContent);
    }

    return $htmlContent;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedOptions = isset($_POST['options']) ? $_POST['options'] : [];
    $customPattern = isset($_POST['customPattern']) ? $_POST['customPattern'] : '';
    
    // Read HTML file content
    $htmlContent = file_get_contents('file:///C:/wamp64/www/mozilor/content.html');

    // Block rendering based on user selection
    $modifiedContent = blockRendering($htmlContent, $selectedOptions, $customPattern);

    // Output modified content
    echo $modifiedContent;
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scripts & Iframes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        form {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        input[type="checkbox"],
        input[type="text"] {
            margin-bottom: 10px;
        }

        button {
            background-color: #4caf50;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>

    <form method="post" action="">
        <label>
            <input type="checkbox" name="options[]" value="googleAds"> Block Google Ads
        </label>
        <br>
        <label>
            <input type="checkbox" name="options[]" value="googleAnalytics"> Block Google Analytics
        </label>
        <br>
        <label>
            <input type="checkbox" name="options[]" value="soundCloud"> Block SoundCloud
        </label>
        <br>
        <label>
            <input type="checkbox" name="options[]" value="youtubeEmbed"> Block YouTube Embed
        </label>
        <br>
        <label>
            Custom Pattern:
            <input type="text" name="customPattern">
        </label>
        <br>
        <button type="submit">Submit</button>
    </form>

</body>

</html>
