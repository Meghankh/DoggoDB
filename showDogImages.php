<html>
<head>
</head>
<body onload="return ran_col()">

        <script type="text/javascript">
            function ran_col() { //function name
                var color = '#'; // hexadecimal starting symbol
                var letters = ['D87AB1','01c8cd','AB3489','F78992','087ADF','223780','ABFBDC','86AB89']; //Set your colors here
                color += letters[Math.floor(Math.random() * letters.length)];
                document.getElementById('main').style.background = color; // Setting the random color on your div element.
            }
        </script>
<div id="main" style = "height:110%;">
<h1>Welcome to Doggo Daycare!</h1>
<h2>Here are some of our latest uploads<h2>
<?php

$imageFolder = 'dogImages/';

$imageTypes = '{*.jpg,*.JPG,*.jpeg,*.JPEG,*.png,*.PNG,*.gif,*.GIF}';

$sortByImageName = false;

$newestImagesFirst = true;

$images = glob($imageFolder . $imageTypes, GLOB_BRACE);

if ($sortByImageName) {
    $sortedImages = $images;
    natsort($sortedImages);
} else {
    $sortedImages = array();
    $count = count($images);
    for ($i = 0; $i < $count; $i++) {
        $sortedImages[date('YmdHis', filemtime($images[$i])) . $i] = $images[$i];
    }
    if ($newestImagesFirst) {
        krsort($sortedImages);
    } else {
        ksort($sortedImages);
    }
}
# Generate the HTML output
foreach ($sortedImages as $image) {
echo '<img src="'.$image.'" style="max-width:'.rand(100,500).'"/>';
}
?>
<div>
</body>
</html>
