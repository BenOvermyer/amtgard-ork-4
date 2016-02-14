$( document ).ready( function () {
  $( 'button.search' ).click( function () {
    var query = $( 'input.player-search' ).val();

    if ( query != '' && query.length >= 3 ) {
      location = '/player/search/' + query;
    } else {
      alert( 'Search query must have at least three characters!' );
    }

    return false;
  } );
} );
//# sourceMappingURL=all.js.map
