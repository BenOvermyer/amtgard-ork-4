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

  $( 'input.player-search' ).keydown( function ( e ) {
    if ( e.keyCode === 13 ) {
      $( 'button.search' ).click();
      return false;
    }
  } );

  $( document ).keydown( function ( e ) {
    if ( !$( 'input' ).is( ':focus' ) ) {
      if ( e.keyCode === 83 ) {
        $( 'input.player-search' ).focus();
        return false;
      }
      else if ( e.keyCode === 72 ) {
        location = '/';
        return false;
      }
    }
  } );
} );
//# sourceMappingURL=all.js.map
