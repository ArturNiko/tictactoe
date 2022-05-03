import '../bootstrap/dist/js/bootstrap.js'

//clear cookie after deleting to prevent double post request
if ( window.history.replaceState ) {
    window.history.replaceState( null, null, window.location.href );
}
