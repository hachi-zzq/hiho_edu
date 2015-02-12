define(function(){
  requirejs.config({
    paths: {
      infrastructure: 'infrastructure',
      jquery: 'lib/jquery-1.11.1',
      blockui: 'lib/jquery.blockUI',
      artdialog: 'lib/artdialog',
      sewise: 'lib/sewise',
      sewisecontrol: 'sewisecontrol',
      subtitle: 'subtitle',
      clip: 'clip',
      subtitleselect: 'subtitleselect',
      comment: 'comment'
    },
    shim: {
      blockui: {
        deps: ['infrastructure']
      },
      artdialog: {
        deps: ['infrastructure']
      }
      /*subtitle: {
        deps: ['sewise']
      },
      clip: {
        deps: ['sewise']
      }*/
    }
  });
});