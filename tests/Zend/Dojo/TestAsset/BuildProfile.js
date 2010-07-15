dependencies = {
    "action":        "release",
    "releaseName":   "zend",
    "loader":        "default",
    "cssOptimize":   "comments",
    "optimize":      "shrinksafe",
    "layerOptimize": "shrinksafe",
    "copyTests":     false,
    "layers": [
        {
            "name": "../zend/main.js",
            "layerDependencies": [],
            "dependencies": [
                "zend.main"
            ]
        }
    ],
    "prefixes": [
        [ "zend", "../zend" ]
    ]
};
