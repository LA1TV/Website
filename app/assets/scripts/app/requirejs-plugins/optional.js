// http://stackoverflow.com/a/27422370/1048589
define("optional", [], {
    load : function (moduleName, parentRequire, onload, config){
        var onLoadSuccess = function(moduleInstance){
            // Module successfully loaded, call the onload callback so that
            // requirejs can work its internal magic.
            onload(moduleInstance);
        }

        var onLoadFailure = function(err){
            // optional module failed to load.
            var failedId = err.requireModules && err.requireModules[0];
            console.log("Could not load optional module: " + failedId);

            // Undefine the module to cleanup internal stuff in requireJS
            requirejs.undef(failedId);

            // Now define the module instance as null
            define(failedId, [], function(){return null;});

            // Now require the module make sure that requireJS thinks 
            // that is it loaded. Since we've just defined it, requirejs 
            // will not attempt to download any more script files and
            // will just call the onLoadSuccess handler immediately
            parentRequire([failedId], onLoadSuccess);
        }

        parentRequire([moduleName], onLoadSuccess, onLoadFailure);
    }
});