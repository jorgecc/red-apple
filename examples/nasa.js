// Create a WorldWindow for the canvas.
var wwd = new WorldWind.WorldWindow("canvasOne");
wwd.pickEnabled=true;
//wwd.deepPicking=true;

wwd.addLayer(new WorldWind.BMNGOneImageLayer());
wwd.addLayer(new WorldWind.BMNGLandsatLayer());

wwd.addLayer(new WorldWind.CompassLayer());
wwd.addLayer(new WorldWind.CoordinatesDisplayLayer(wwd));
wwd.addLayer(new WorldWind.ViewControlsLayer(wwd));

function addPlacemark(lat,long,text,img) {
    let placemarkLayer = new WorldWind.RenderableLayer();
    wwd.addLayer(placemarkLayer);

    var placemarkAttributes = new WorldWind.PlacemarkAttributes(null);

    placemarkAttributes.imageOffset = new WorldWind.Offset(
        WorldWind.OFFSET_FRACTION, 0.3,
        WorldWind.OFFSET_FRACTION, 0.0);

    placemarkAttributes.labelAttributes.offset = new WorldWind.Offset(
        WorldWind.OFFSET_FRACTION, 0.5,
        WorldWind.OFFSET_FRACTION, 1.0);

    placemarkAttributes.imageSource = img; // WorldWind.configuration.baseUrl + "images/pushpins/plain-red.png";

    var position = new WorldWind.Position(lat, long, 100.0);
    var placemark = new WorldWind.Placemark(position, false, placemarkAttributes);

    placemark.label = text;
    placemark.alwaysOnTop = true;

    placemarkLayer.pickEnabled=true;
    placemarkLayer.addRenderable(placemark);
    
    //var clickRecognizer3 = new WorldWind.ClickRecognizer(placemarkLayer, handleClick2);
    
    console.log(placemark);
    //
}
//addPlacemark(55,-106,'hello world');
//addPlacemark(55,-140,'hello world');
var handlePick = function (o) {
    console.log(o);
};

var handleClick2 = function (recognizer) {
    // Obtain the event location.
    var x = recognizer.clientX,
        y = recognizer.clientY;

    // Perform the pick. Must first convert from window coordinates to canvas coordinates, which are
    // relative to the upper left corner of the canvas rather than the upper left corner of the page.
    var pickList = wwd.pick(wwd.canvasCoordinates(x, y));

    // If only one thing is picked and it is the terrain, tell the WorldWindow to go to the picked location.
    if (pickList.objects.length >1 && pickList.objects[0].isTerrain===false) {
        console.log(pickList.objects[0].position.latitude);
        console.log(pickList.objects[0].position.longitude);
        console.log(pickList.objects[0].userObject.label);
    }
    /*if (pickList.objects.length === 1 && pickList.objects[0].isTerrain) {
        
        var position = pickList.objects[0].position;
        wwd.goTo(new WorldWind.Location(position.latitude, position.longitude));
    }*/
};

wwd.addEventListener("mousemove", handleClick2);

// Listen for mouse clicks.
var clickRecognizer = new WorldWind.ClickRecognizer(wwd, handleClick2);

// Listen for taps on mobile devices.
var tapRecognizer = new WorldWind.TapRecognizer(wwd, handleClick2);

//wwd.addEventListener("click", handlePick);

