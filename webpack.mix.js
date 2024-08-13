const mix = require('laravel-mix');

mix.js('resources/js/Dashboard/trangChuTongQuat.js', 'public/js/Dashboard')
   .js('resources/js/Dashboard/trangChuKeHoach.js', 'public/js/Dashboard')
   .js('resources/js/Warehouse Management/Inside/quanlykehoach.js', 'public/js/Warehouse Management/Inside')
   .postCss('resources/css/Dashboard/trangChuKeHoach.css', 'public/css/Dashboard')
   .postCss('resources/css/Dashboard/trangChuTongQuat.css', 'public/css/Dashboard')
   .postCss('resources/css/Warehouse Management/Inside/quanlykehoach.css', 'public/css/Warehouse Management/Inside');

