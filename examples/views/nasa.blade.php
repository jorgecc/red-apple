<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
</head>
<body>
<canvas id="canvasOne" width="1024" height="768">
    Your browser does not support HTML5 Canvas.
</canvas>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script
        src="https://files.worldwind.arc.nasa.gov/artifactory/web/0.9.0/worldwind.min.js"
        type="text/javascript">
</script>
<script src="nasa.js" type="text/javascript"></script>
<script>
    {!! $placemarks !!}
</script>


</body>
</html>