
//Remove hash in URL sent from facebook login
if(window.location.hash && window.location.hash == "#_=_") {
    // Fragment exists
    history.pushState('', document.title, window.location.pathname);
} else {
  // Fragment doesn't exist
}