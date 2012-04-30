TinyBox - Simple, no requirements JS popups
==================

<http://www.scriptiny.com/2011/03/javascript-modal-windows/>

To activate, add the following to your template:

    <script src="{$_EXTERNAL_CACHE_}/javascripts/tinybox2/packed.js" charset="utf-8"></script>
    <link rel="stylesheet" href="{$_EXTERNAL_CACHE_}/javascripts/tinybox2/style.css" charset="utf-8" />

And test it out:
    
    TINY.box.show({html:'This is a warning!',animate:false,close:false,boxid:'error',top:5})