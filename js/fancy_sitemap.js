(function($) {
    Raphael.fn.connection = function (obj1, obj2, line, bg) {
        if (obj1.line && obj1.from && obj1.to) {
            line = obj1;
            obj1 = line.from;
            obj2 = line.to;
        }
        var bb1 = obj1.getBBox(),
        bb2 = obj2.getBBox(),
        p = [{
            x: bb1.x + bb1.width / 2, 
            y: bb1.y - 1
        },

        {
            x: bb1.x + bb1.width / 2, 
            y: bb1.y + bb1.height + 1
        },

        {
            x: bb1.x - 1, 
            y: bb1.y + bb1.height / 2
        },

        {
            x: bb1.x + bb1.width + 1, 
            y: bb1.y + bb1.height / 2
        },

        {
            x: bb2.x + bb2.width / 2, 
            y: bb2.y - 1
        },

        {
            x: bb2.x + bb2.width / 2, 
            y: bb2.y + bb2.height + 1
        },

        {
            x: bb2.x - 1, 
            y: bb2.y + bb2.height / 2
        },

        {
            x: bb2.x + bb2.width + 1, 
            y: bb2.y + bb2.height / 2
        }],
        d = {}, dis = [];
        for (var i = 0; i < 4; i++) {
            for (var j = 4; j < 8; j++) {
                var dx = Math.abs(p[i].x - p[j].x),
                dy = Math.abs(p[i].y - p[j].y);
                if ((i == j - 4) || (((i != 3 && j != 6) || p[i].x < p[j].x) && ((i != 2 && j != 7) || p[i].x > p[j].x) && ((i != 0 && j != 5) || p[i].y > p[j].y) && ((i != 1 && j != 4) || p[i].y < p[j].y))) {
                    dis.push(dx + dy);
                    d[dis[dis.length - 1]] = [i, j];
                }
            }
        }
        if (dis.length == 0) {
            var res = [0, 4];
        } else {
            res = d[Math.min.apply(Math, dis)];
        }
        var x1 = p[res[0]].x,
        y1 = p[res[0]].y,
        x4 = p[res[1]].x,
        y4 = p[res[1]].y;
        dx = Math.max(Math.abs(x1 - x4) / 2, 10);
        dy = Math.max(Math.abs(y1 - y4) / 2, 10);
        var x2 = [x1, x1, x1 - dx, x1 + dx][res[0]].toFixed(3),
        y2 = [y1 - dy, y1 + dy, y1, y1][res[0]].toFixed(3),
        x3 = [0, 0, 0, 0, x4, x4, x4 - dx, x4 + dx][res[1]].toFixed(3),
        y3 = [0, 0, 0, 0, y1 + dy, y1 - dy, y4, y4][res[1]].toFixed(3);
        var path = ["M", x1.toFixed(3), y1.toFixed(3), "C", x2, y2, x3, y3, x4.toFixed(3), y4.toFixed(3)].join(",");
        if (line && line.line) {
            line.bg && line.bg.attr({
                path: path
            });
            line.line.attr({
                path: path
            });
        } else {
            var color = typeof line == "string" ? line : "#000";
            return {
                bg: bg && bg.split && this.path(path).attr({
                    stroke: bg.split("|")[0], 
                    fill: "none", 
                    "stroke-width": bg.split("|")[1] || 3
                }),
                line: this.path(path).attr({
                    stroke: color, 
                    fill: "none",
                    "stroke-width":options.line_width
                }),
                from: obj1,
                to: obj2
            };
        }
    };
            
    $(document).ready(function(){
        var sitemap = cleanHtml($('.fancySitemap')).hide();
        var holder = $('#sitemapHolder');
        var r = Raphael("sitemapHolder", holder.width(), holder.height());
        var connections = [];
        var tree = [];
        
        $('#savePosition').hide();
        
        var dragger = function () {
            this.ox = this.attr("x");
            this.oy = this.attr("y");
            this.animate({
                "fill-opacity": .2
            }, 500);
        };
        
        var move = function (dx, dy) {
            var att = {
                x: this.ox + dx, 
                y: this.oy + dy
            };

            this.attr(att);
            this.text.attr({
                x:(att.x + this.attrs.width/2), 
                y:(att.y + this.attrs.height/2)
            });
            
            for (var i = connections.length; i--;) {
                r.connection(connections[i]);
            }
            r.safari();
        };
        
        var up = function () {
            this.animate({
                "fill-opacity": 0
            }, 500).animate({
                "fill-opacity": 1
            }, 500);
            
            if(preview){
                var canvasWidth = holder.width();
                var positions = [];
                var positionInput = $('#positionInput').length?$('#positionInput'):$('<input />',{
                    name:'positions',
                    id:'positionInput',
                    type:'hidden'
                }).appendTo(holder);
                
                positions = getTreePosition(tree, positions, canvasWidth);
                for(i=0;i<positions.length;i++){
                    positionInput.val(positionInput.val() + positions[i] + '||');
                }
                $('#savePosition').fadeIn();
            }
        };
        
        /* 
         * clean tree html before processing
         */
        function cleanHtml(list){
            var html = list.html();
            
            html = html.replace(/<\/?strong>/gi, '');
            
            list.html(html);
            return list;
        }
        
        /*
         * generate a string containing positions of all nodes
         */
        function getTreePosition(tree, positions, w){
            if(tree != null){
                var i = 0;
                for(;i<tree.length;i++){
                    positions.push(tree[i].id + ',' + tree[i].shape.attr("x")/w + ',' + tree[i].shape.attr("y"));
                    getTreePosition(tree[i].children, positions, w);
                }
                return positions;
            }
            return null;
        }
        
        /*
         * parent = root or subroot of a tree
         * canvasWidth = canvas width
         * r = Raphael object
         * d = depth
         */
        function fillTree(parent, r, canvasWidth, d){
            var children = parent.children('li');
            if(children.length){
                var tree = new Array(children.length);
                children.each(function(i){
                    var node = $(this);
                    var link = node.children('a');
                    var id = node.attr('class').replace(/\D/g, '');
                    var w = parseInt(options.width), h = parseInt(options.height);
                    var x = (canvasWidth/(children.length+1)) * (node.index()+1) - w/2;
                    var y = d * (h+40) + 10;
                    
                    if(positions[id]){
                        x = positions[id].x * canvasWidth;
                        y = positions[id].y;
                    }
                    
                    tree[i] = new function(){
                        this.id = id;
                        this.cornerRadius = node.children('ul').children('li').length?5:15;
                        this.title = link.text();
                        this.url = link.attr('href');
                        this.children = fillTree(node.children('ul'), r, canvasWidth, d+1);
                        this.shape = r.rect(x, y, w, h, this.cornerRadius).attr({
                            cursor:"pointer"
                        }).hover(function(){
                            this.animate({
                                fill: options.background_hover_color
                            }, 500);
                        },function(){
                            this.animate({
                                fill: options.background_color
                            }, 500);
                        });
                        
                        if(typeof preview !== 'undefined' && preview==true)
                            this.shape.drag(move, dragger, up);
                        
                        this.shape.text = r.text(x, y+h/2, link.text()).attr({
                            fill:options.font_color,
                            'font-size':options.font_size,
                            href: this.url,
                            target: "_blank",
                            title: this.title,
                            cursor:"pointer"
                        })
                        
                        if(options.font_size_hover != options.font_size){
                            this.shape.text.hover(function(){
                                this.animate({
                                    'font-size':options.font_size_hover
                                },100).toFront();
                        
                            },function(){
                                this.animate({
                                    'font-size':options.font_size
                                },100);
                            });
                        }
                        
                        //attempt to autosize block
                        if(options.auto_size == '1'){
                            var textWidth = this.shape.text.node.getSubStringLength(0, this.title.length);
                            this.shape.attr({
                                width:textWidth + 30
                                });
                        }
                        this.shape.text.attr({
                            x: x + this.shape.attr('width')/2
                            });
                    };
                });
                return tree;
            }
            return null;
        }
    
        function buildTree(tree, parent, c, r){
            if(tree != null){
                var i = 0;
                for(;i<tree.length;i++){
                    tree[i].shape.attr({
                        fill: options.background_color,
                        stroke: options.border_color,
                        "fill-opacity": 1, 
                        "stroke-width": 2
                    });                
                
                    buildTree(tree[i].children, tree[i].shape, c, r);
                
                    if(parent != null && tree[i] != null)
                        c.push(r.connection(parent, tree[i].shape, options.line_color));
                }
                return tree;
            }
            return null;
        }
        
        tree = fillTree(sitemap, r, holder.width(), 0);
        
        buildTree(tree, null, connections, r);
    });
})(jQuery);