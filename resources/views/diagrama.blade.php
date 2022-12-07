@extends('layouts.app')
@section('content')
    <!DOCTYPE html>
    <html lang="en">


    {{-- Socket IO --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/4.5.1/socket.io.js"
        integrity="sha512-9mpsATI0KClwt+xVZfbcf2lJ8IFBAwsubJ6mI3rtULwyM3fBmQFzj0It4tGqxLOGQwGfJdk/G+fANnxfq9/cew=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script type="importmap">
    {
      "imports": {
        "socket.io-client": "https://cdn.socket.io/4.4.1/socket.io.esm.min.js"
      }
    }
        </script>
    <script type="module">
        import { io } from "socket.io-client";

    </script>

    <body>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end mx-2 ">

            @foreach ($document as $item)
                <h2 class="me-auto justify-content-start mx-2">Diagrama: {{ $item->name }}</h2>
            @endforeach
            <div class="m-2 no-print">

                <button class="btn btn-success" onclick=save()>Guardar mi Diagrama</button>
                <button class="btn btn-danger" type="button" onclick=eliminar()>Limpiar Vista</button>
                <button class="btn btn-primary" type="button" onclick=convertirJPG()>Exportar JPG</button>
                <button class="btn btn-primary" type="button"  onclick=pdf()>Exportar PDF</button>
                <button class="btn btn-primary" type="button" >Exportar json</button>
                <button class="btn btn-secondary" type="button" onclick=imprimir()>Imprimir</button>

            </div>



        </div>

        <script src="https://unpkg.com/gojs@2.2.17/release/go.js"></script>
        <div id="allSampleContent" class="pt-2 px-4 w-full">

            <script src="https://unpkg.com/gojs@2.2.17/extensions/Figures.js"></script>
            <script src="https://unpkg.com/gojs@2.2.17/extensions/DrawCommandHandler.js"></script>
            <script id="code">
                const socket = io("http://52.55.84.103:3000/", {
                    transports: ["websocket"]
                });

                const sala = "{{ $id }}";
                socket.emit('join-room', sala)
                socket.on('broadcast', () => {
                    console.log('mensaje broadcast')
                })

                function init() {

                    //iniciar diagrama
                    // Since 2.2 you can also author concise templates with method chaining instead of GraphObject.make
                    // For details, see https://gojs.net/latest/intro/buildingObjects.html
                    const $ = go.GraphObject.make;
                    myDiagram =
                        $(go.Diagram, "myDiagramDiv", {
                            padding: 20, // extra space when scrolled all the way
                            grid: $(go.Panel, "Grid", // a simple 10x10 grid
                                $(go.Shape, "LineH", {
                                    stroke: "lightgray",
                                    strokeWidth: 0.5
                                }),
                                $(go.Shape, "LineV", {
                                    stroke: "lightgray",
                                    strokeWidth: 0.5
                                })
                            ),
                            "animationManager.isEnabled": false,
                            //control z
                            "undoManager.isEnabled": true,
                            //copiar
                            "commandHandler.copiesTree": true,


                            "draggingTool.isGridSnapEnabled": true,
                            handlesDragDropForTopLevelParts: true,
                            mouseDrop: e => {
                                // when the selection is dropped in the diagram's background,
                                // make sure the selected Parts no longer belong to any Group
                                console.log('moviendose')
                                var ok = e.diagram.commandHandler.addTopLevelParts(e.diagram.selection, true);
                                if (!ok) e.diagram.currentTool.doCancel();

                                socket.emit('actualizar', myDiagram.model.toJSON(), sala)
                                //saveInDB();
                            },
                            commandHandler: $(DrawCommandHandler),

                            // support offset copy-and-paste
                            // create a new node by double-clicking in background
                            "clickCreatingTool.archetypeNodeData": {
                                text: "NEW NODE",
                                fill: "transparent",
                                stroke: "darkblue",
                                strokeWidth: 2,


                            },

                            "PartCreated": e => {
                                //agregar aca para cuando se cree nuevo nodo

                                socket.emit('actualizar', myDiagram.model.toJSON(), sala)

                                var node = e
                                    .subject; // the newly inserted Node -- now need to snap its location to the grid
                                node.location = node.location.copy().snapToGridPoint(e.diagram.grid.gridOrigin, e.diagram
                                    .grid.gridCellSize);
                                setTimeout(() => { // and have the user start editing its text
                                    e.diagram.commandHandler.editTextBlock();

                                    //  socket.emit('actualizar', myDiagram.model.toJSON(), 3)


                                }, 20);
                            },
                            "commandHandler.archetypeGroupData": {
                                isGroup: true,
                                text: "NEW GROUPf"

                            },
                            //crear grupo
                            "SelectionGrouped": e => {
                                //agregar aca para cuando se cree grupo
                                //socket.emit('actualizar', myDiagram.model.toJSON(), 3)

                                var group = e.subject;
                                setTimeout(() => { // and have the user start editing its text
                                    e.diagram.commandHandler.editTextBlock();
                                    socket.emit('actualizar', myDiagram.model.toJSON(), sala)



                                })
                            },
                            "LinkRelinked": e => {
                                // re-spread the connections of other links connected with both old and new nodes
                                var oldnode = e.parameter.part;
                                oldnode.invalidateConnectedLinks();
                                var link = e.subject;
                                if (e.diagram.toolManager.linkingTool.isForwards) {
                                    link.toNode.invalidateConnectedLinks();

                                } else {
                                    link.fromNode.invalidateConnectedLinks();
                                }
                            },
                            "undoManager.isEnabled": true
                        });

                    //para los links
                    myDiagram.addDiagramListener("LinkDrawn", e => {
                        if (myDiagram.isModified) {
                            socket.emit('actualizar', myDiagram.model.toJSON(), sala)
                            console.log('podria ser')
                        }
                    });
                    //cuando se borra
                    myDiagram.addDiagramListener("SelectionDeleted", e => {
                        if (myDiagram.isModified) {
                            socket.emit('actualizar', myDiagram.model.toJSON(), sala)
                            console.log('podria ser')
                        }
                    });

                    //grupo

                    myDiagram.addDiagramListener("SelectionGrouped", e => {
                        if (myDiagram.isModified) {
                            socket.emit('actualizar', myDiagram.model.toJSON(), sala)
                            console.log('podria ser')
                        }
                    });
                    myDiagram.addDiagramListener("SelectionUngrouped", e => {
                        if (myDiagram.isModified) {
                            socket.emit('actualizar', myDiagram.model.toJSON(), sala)
                            console.log('podria ser')
                        }
                    });



                    //para texto
                    myDiagram.addDiagramListener("TextEdited", e => {
                        if (myDiagram.isModified) {
                            socket.emit('actualizar', myDiagram.model.toJSON(), sala)
                            console.log('podria ser')
                        }
                    });
                    //para texto




                    // Node template


                    //prueba aqui


                    //////////////////////////////////


                    //aqui ciego
                    myDiagram.nodeTemplate =
                        $(go.Node, "Auto", {
                                locationSpot: go.Spot.Center,
                                locationObjectName: "SHAPE",
                                desiredSize: new go.Size(120, 60),
                                minSize: new go.Size(40, 40),
                                resizable: true,
                                resizeCellSize: new go.Size(20, 20)



                            },
                            // these Bindings are TwoWay because the DraggingTool and ResizingTool modify the target properties
                            new go.Binding("location", "loc", go.Point.parse).makeTwoWay(go.Point.stringify),
                            new go.Binding("desiredSize", "size", go.Size.parse).makeTwoWay(go.Size.stringify),
                            $(go.Shape, { // the border
                                    name: "SHAPE",
                                    fill: "white",
                                    portId: "",
                                    cursor: "pointer",
                                    fromLinkable: true,
                                    toLinkable: true,
                                    fromLinkableDuplicates: true,
                                    toLinkableDuplicates: true,
                                    fromSpot: go.Spot.AllSides,
                                    toSpot: go.Spot.AllSides
                                },
                                new go.Binding("figure"),
                                new go.Binding("fill"),
                                new go.Binding("stroke", "color"),
                                new go.Binding("strokeWidth", "thickness"),
                                new go.Binding("strokeDashArray", "dash")),



                            // this Shape prevents mouse events from reaching the middle of the port
                            $(go.Shape, {
                                width: 100,
                                height: 40,
                                strokeWidth: 0,
                                fill: "transparent"
                            }),
                            $(go.TextBlock, {
                                    margin: 1,
                                    textAlign: "center",
                                    overflow: go.TextBlock.OverflowEllipsis,
                                    editable: true
                                },
                                // this Binding is TwoWay due to the user editing the text with the TextEditingTool
                                new go.Binding("text").makeTwoWay(),
                                new go.Binding("stroke", "color")),



                            makePort("T", go.Spot.Top, false, true),
                            makePort("L", go.Spot.Left, true, true),
                            makePort("R", go.Spot.Right, true, true),
                            makePort("B", go.Spot.Bottom, true, false),
                            //seleccionar figura mostrar o ocultar puertos
                            {
                                mouseEnter: (e, node) => showSmallPorts(node, true),
                                mouseLeave: (e, node) => showSmallPorts(node, false)
                            }





                        );

                    myDiagram.nodeTemplate.toolTip =
                        $("ToolTip", // show some detailed information
                            $(go.Panel, "Vertical", {
                                    maxSize: new go.Size(200, NaN)
                                }, // limit width but not height
                                $(go.TextBlock, {
                                        font: "bold 10pt sans-serif",
                                        textAlign: "center"
                                    },
                                    new go.Binding("text")),
                                $(go.TextBlock, {
                                        font: "10pt sans-serif",
                                        textAlign: "center"
                                    },
                                    new go.Binding("text", "details"))
                            )
                        );

                    // Node selection adornment
                    // Include four large triangular buttons so that the user can easily make a copy
                    // of the node, move it to be in that direction relative to the original node,
                    // and add a link to the new node.

                    function showSmallPorts(node, show) {

                        node.ports.each(port => {
                            if (port.portId !== "") { // don't change the default port, which is the big shape
                                port.fill = show ? "rgba(255,0,0,.8)" : null;
                            }
                        });


                    }

                    function makePort(name, spot, output, input) {
                        // the port is basically just a small transparent circle
                        return $(go.Shape, "Circle", {
                            fill: null, // not seen, by default; set to a translucent gray by showSmallPorts, defined below
                            stroke: null,
                            desiredSize: new go.Size(7, 7),
                            alignment: spot, // align the port on the main Shape
                            alignmentFocus: spot, // just inside the Shape
                            portId: name, // declare this object to be a "port"
                            fromSpot: spot,
                            toSpot: spot, // declare where links may connect at this port
                            fromLinkable: output,
                            toLinkable: input, // declare whether the user may draw links to/from here
                            cursor: "pointer" // show a different cursor to indicate potential link point
                        });
                    }
                    //flechas del diagrama
                    // create a button that brings up the context menu
                    function CMButton(options) {
                        return $(go.Shape, {
                                fill: "red",
                                stroke: "gray",
                                background: "transparent",
                                geometryString: "F1 M0 0 M0 4h4v4h-4z M6 4h4v4h-4z M12 4h4v4h-4z M0 12",
                                isActionable: true,
                                cursor: "context-menu",
                                click: (e, shape) => {
                                    e.diagram.commandHandler.showContextMenu(shape.part.adornedPart);
                                }
                            },

                            options || {});
                    }
                    //seleccion de figura adorno cuando esta selecionada
                    myDiagram.nodeTemplate.selectionAdornmentTemplate =
                        $(go.Adornment, "Spot",
                            $(go.Placeholder, {
                                padding: 10
                            }),

                            CMButton({
                                alignment: new go.Spot(0.75, 0)
                            })
                        );

                    // Common context menu button definitions

                    // All buttons in context menu work on both click and contextClick,
                    // in case the user context-clicks on the button.
                    // All buttons modify the node data, not the Node, so the Bindings need not be TwoWay.

                    // A button-defining helper function that returns a click event handler.
                    // PROPNAME is the name of the data property that should be set to the given VALUE.
                    function ClickFunction(propname, value) {

                        return (e, obj) => {
                            e.handled = true; // don't let the click bubble up
                            e.diagram.model.commit(m => {
                                m.set(obj.part.adornedPart.data, propname, value);

                            });
                        };
                    }

                    // Create a context menu button for setting a data property with a color value.
                    function ColorButton(color, propname) {
                        if (!propname) propname = "color";
                        return $(go.Shape, {
                            width: 16,
                            height: 16,
                            stroke: "lightgray",
                            fill: color,
                            margin: 1,
                            background: "transparent",
                            mouseEnter: (e, shape) => shape.stroke = "dodgerblue",
                            mouseLeave: (e, shape) => shape.stroke = "lightgray",
                            click: ClickFunction(propname, color),
                            contextClick: ClickFunction(propname, color)
                        });

                    }

                    function LightFillButtons() { // used by multiple context menus

                        return [
                            $("ContextMenuButton",
                                $(go.Panel, "Horizontal",
                                    ColorButton("white", "fill"), ColorButton("lightskyblue", "fill"), ColorButton("aliceblue",
                                        "fill"), ColorButton("lightblue", "fill")
                                )
                            ),
                            $("ContextMenuButton",
                                $(go.Panel, "Horizontal",
                                    ColorButton("lightgray", "fill"), ColorButton("lightgreen", "fill"), ColorButton(
                                        "lightblue", "fill"), ColorButton("pink", "fill")
                                )
                            )
                        ];

                    }

                    function DarkColorButtons() { // used by multiple context menus
                        return [
                            $("ContextMenuButton",
                                $(go.Panel, "Horizontal",
                                    ColorButton("black"), ColorButton("green"), ColorButton("blue"), ColorButton("red")
                                )
                            ),
                            $("ContextMenuButton",
                                $(go.Panel, "Horizontal",
                                    ColorButton("brown"), ColorButton("magenta"), ColorButton("purple"), ColorButton("orange")
                                )
                            )
                        ];
                    }

                    // Create a context menu button for setting a data property with a stroke width value.
                    function ThicknessButton(sw, propname) {
                        if (!propname) propname = "thickness";
                        return $(go.Shape, "LineH", {
                            width: 16,
                            height: 16,
                            strokeWidth: sw,
                            margin: 1,
                            background: "transparent",
                            mouseEnter: (e, shape) => shape.background = "dodgerblue",
                            mouseLeave: (e, shape) => shape.background = "transparent",
                            click: ClickFunction(propname, sw),
                            contextClick: ClickFunction(propname, sw)
                        });
                    }

                    // Create a context menu button for setting a data property with a stroke dash Array value.
                    function DashButton(dash, propname) {
                        if (!propname) propname = "dash";
                        return $(go.Shape, "LineH", {
                            width: 24,
                            height: 16,
                            strokeWidth: 2,
                            strokeDashArray: dash,
                            margin: 1,
                            background: "transparent",
                            mouseEnter: (e, shape) => shape.background = "dodgerblue",
                            mouseLeave: (e, shape) => shape.background = "transparent",
                            click: ClickFunction(propname, dash),
                            contextClick: ClickFunction(propname, dash)
                        });
                    }

                    function StrokeOptionsButtons() { // used by multiple context menus
                        return [
                            $("ContextMenuButton",
                                $(go.Panel, "Horizontal",
                                    ThicknessButton(1), ThicknessButton(2), ThicknessButton(3), ThicknessButton(4)
                                )
                            ),
                            $("ContextMenuButton",
                                $(go.Panel, "Horizontal",
                                    DashButton(null), DashButton([2, 4]), DashButton([4, 4])
                                )
                            )
                        ];
                    }

                    // Node context menu

                    function FigureButton(fig, propname) {
                        if (!propname) propname = "figure";
                        return $(go.Shape, {
                            width: 32,
                            height: 32,
                            scale: 0.5,
                            fill: "lightgray",
                            figure: fig,
                            margin: 1,
                            background: "transparent",
                            mouseEnter: (e, shape) => shape.fill = "dodgerblue",
                            mouseLeave: (e, shape) => shape.fill = "lightgray",
                            click: ClickFunction(propname, fig),
                            contextClick: ClickFunction(propname, fig)
                        });
                    }

                    myDiagram.nodeTemplate.contextMenu =
                        $("ContextMenu",

                            LightFillButtons(),
                            DarkColorButtons(),
                            StrokeOptionsButtons()
                        );


                    // Group template


                    myDiagram.groupTemplate =
                        $(go.Group, "Spot", {
                                layerName: "Background",
                                ungroupable: true,
                                locationSpot: go.Spot.Center,
                                selectionObjectName: "BODY",
                                computesBoundsAfterDrag: true, // allow dragging out of a Group that uses a Placeholder
                                handlesDragDropForMembers: true, // don't need to define handlers on Nodes and Links
                                mouseDrop: (e, grp) => { // add dropped nodes as members of the group
                                    var ok = grp.addMembers(grp.diagram.selection, true);
                                    if (!ok) grp.diagram.currentTool.doCancel();


                                },
                                avoidable: false
                            },
                            new go.Binding("location", "loc", go.Point.parse).makeTwoWay(go.Point.stringify),
                            $(go.Panel, "Auto", {
                                    name: "BODY"
                                },
                                $(go.Shape, {
                                        parameter1: 10,
                                        fill: "white",
                                        strokeWidth: 2,
                                        portId: "",
                                        cursor: "pointer",
                                        fromLinkable: true,
                                        toLinkable: true,
                                        fromLinkableDuplicates: true,
                                        toLinkableDuplicates: true,
                                        fromSpot: go.Spot.AllSides,
                                        toSpot: go.Spot.AllSides
                                    },
                                    new go.Binding("fill"),
                                    new go.Binding("stroke", "color"),
                                    new go.Binding("strokeWidth", "thickness"),
                                    new go.Binding("strokeDashArray", "dash")),
                                $(go.Placeholder, {
                                    background: "transparent",
                                    margin: 10
                                })
                            ),
                            $(go.TextBlock, {
                                    alignment: go.Spot.Top,
                                    alignmentFocus: go.Spot.Bottom,
                                    font: "bold 12pt sans-serif",
                                    editable: true
                                },
                                new go.Binding("text"),
                                new go.Binding("stroke", "color"))
                        );

                    myDiagram.groupTemplate.selectionAdornmentTemplate =
                        $(go.Adornment, "Spot",
                            $(go.Panel, "Auto",
                                $(go.Shape, {
                                    fill: null,
                                    stroke: "dodgerblue",
                                    strokeWidth: 3
                                }),
                                $(go.Placeholder, {
                                    margin: 1.5
                                })
                            ),
                            CMButton({

                                alignment: go.Spot.TopRight,
                                alignmentFocus: go.Spot.BottomRight
                            })
                        );

                    myDiagram.groupTemplate.contextMenu =
                        $("ContextMenu",
                            LightFillButtons(),
                            DarkColorButtons(),
                            StrokeOptionsButtons()
                        );





                    // initialize the Palette that is on the left side of the page
                    //mi paleta
                    myPalette =
                        $(go.Palette, "myPaletteDiv", // must name or refer to the DIV HTML element
                            {
                                maxSelectionCount: 1,
                                nodeTemplateMap: myDiagram.nodeTemplateMap, // share the templates used by myDiagram
                                linkTemplate: // simplify the link template, just in this Palette
                                    $(go.Link, { // because the GridLayout.alignment is Location and the nodes have locationSpot == Spot.Center,
                                            // to line up the Link in the same manner we have to pretend the Link has the same location spot
                                            locationSpot: go.Spot.Center,

                                        },

                                    ),

                                // Contenido de paleta
                                model: new go.GraphLinksModel([ // specify the contents of the Palette
                                        {
                                            figure: "Actor",
                                            "size": "70 60",
                                            text: 'User',
                                            fill: "lightyellow"
                                        },
                                        {
                                            figure: "Database",
                                            "size": "60 70",
                                            fill: "lightyellow",
                                            text: 'Database',

                                        },
                                        {
                                            figure: "package",
                                            "size": "60 60",
                                            text: 'Paquete',
                                            fill: "lightyellow"
                                        },
                                        {
                                            figure: "Border",
                                            "size": "60 60",
                                            fill: "lightyellow"
                                        },
                                        {
                                            figure: "Class",
                                            "size": "60 60",
                                            fill: "lightyellow"
                                        },
                                        {
                                            figure: "LineH",
                                            "size": "60 60",
                                            fill: "lightyellow"
                                        },
                                        {
                                            figure: "LineV",
                                            "size": "60 60",
                                            fill: "lightyellow"
                                        },
                                        {
                                            figure: "Cloud",
                                            "size": "70 50",
                                            text: "Cloud",
                                            fill: "lightyellow",
                                        },
                                        {
                                            figure: "file",
                                            "size": "90 10",
                                            text: "Comentario",
                                            fill: "lightyellow",
                                        }

                                    ],
                                    [

                                    ])


                            }
                        );

                    // Link template



                    myDiagram.linkTemplate =
                        $(go.Link, {
                                layerName: "Foreground",
                                routing: go.Link.AvoidsNodes,
                                corner: 10,
                                toShortLength: 4, // assume arrowhead at "to" end, need to avoid bad appearance when path is thick
                                relinkableFrom: true,
                                relinkableTo: true,
                                reshapable: true,
                                resegmentable: true
                            },
                            new go.Binding("fromSpot", "fromSpot", go.Spot.parse),
                            new go.Binding("toSpot", "toSpot", go.Spot.parse),
                            new go.Binding("fromShortLength", "dir", dir => dir === 2 ? 4 : 0),
                            new go.Binding("toShortLength", "dir", dir => dir >= 1 ? 4 : 0),
                            new go.Binding("points").makeTwoWay(), // TwoWay due to user reshaping with LinkReshapingTool
                            $(go.Shape, {
                                    strokeWidth: 2
                                },
                                new go.Binding("stroke", "color"),
                                new go.Binding("strokeWidth", "thickness"),
                                new go.Binding("strokeDashArray", "dash"),
                            ),
                            $(go.Shape, {
                                    fromArrow: "Backward",
                                    strokeWidth: 0,
                                    scale: 4 / 3,
                                    visible: false
                                },
                                new go.Binding("visible", "dir", dir => dir === 2),
                                new go.Binding("fill", "color"),
                                new go.Binding("scale", "thickness", t => (2 + t) / 3)),
                            $(go.Shape, {
                                    toArrow: "Standard",
                                    strokeWidth: 0,
                                    scale: 4 / 3
                                },
                                new go.Binding("visible", "dir", dir => dir >= 1),
                                new go.Binding("fill", "color"),
                                new go.Binding("scale", "thickness", t => (2 + t) / 3)),
                            $(go.TextBlock, {
                                    alignmentFocus: new go.Spot(0, 1, -4, 0),
                                    editable: true
                                },

                                new go.Binding("text").makeTwoWay(), // TwoWay due to user editing with TextEditingTool
                                new go.Binding("stroke", "color")),
                        );




                    myDiagram.linkTemplate.selectionAdornmentTemplate =
                        $(go.Adornment, // use a special selection Adornment that does not obscure the link path itself
                            $(go.Shape, { // this uses a pathPattern with a gap in it, in order to avoid drawing on top of the link path Shape
                                    isPanelMain: true,
                                    stroke: "transparent",
                                    strokeWidth: 6,
                                    pathPattern: makeAdornmentPathPattern(2) // == thickness or strokeWidth
                                },
                                new go.Binding("pathPattern", "thickness", makeAdornmentPathPattern)),



                            CMButton({
                                alignmentFocus: new go.Spot(0, 0, -6, -4)
                            })

                        );


                    function makeAdornmentPathPattern(w) {
                        return $(go.Shape, {
                            stroke: "dodgerblue",
                            strokeWidth: 2,
                            strokeCap: "square",
                            geometryString: "M0 0 M4 2 H3 M4 " + (w + 4).toString() + " H3"
                        });
                    }

                    // Link context menu
                    // All buttons in context menu work on both click and contextClick,
                    // in case the user context-clicks on the button.
                    // All buttons modify the link data, not the Link, so the Bindings need not be TwoWay.

                    function ArrowButton(num) {
                        var geo = "M0 0 M16 16 M0 8 L16 8  M12 11 L16 8 L12 5";
                        if (num === 0) {
                            geo = "M0 0 M16 16 M0 8 L16 8";
                        } else if (num === 2) {
                            geo = "M0 0 M16 16 M0 8 L16 8  M12 11 L16 8 L12 5  M4 11 L0 8 L4 5";
                        }
                        return $(go.Shape, {
                            geometryString: geo,
                            margin: 2,
                            background: "transparent",
                            mouseEnter: (e, shape) => shape.background = "dodgerblue",
                            mouseLeave: (e, shape) => shape.background = "transparent",
                            click: ClickFunction("dir", num),
                            contextClick: ClickFunction("dir", num)
                        });
                    }

                    function AllSidesButton(to) {
                        var setter = (e, shape) => {
                            e.handled = true;
                            e.diagram.model.commit(m => {

                                var link = shape.part.adornedPart;
                                m.set(link.data, (to ? "toSpot" : "fromSpot"), go.Spot.stringify(go.Spot
                                    .AllSides));
                                // re-spread the connections of other links connected with the node
                                (to ? link.toNode : link.fromNode).invalidateConnectedLinks();
                            });
                        };
                        return $(go.Shape, {
                            width: 12,
                            height: 12,
                            fill: "transparent",
                            mouseEnter: (e, shape) => shape.background = "dodgerblue",
                            mouseLeave: (e, shape) => shape.background = "transparent",

                            click: setter,
                            contextClick: setter
                        });
                    }

                    function SpotButton(spot, to) {
                        var ang = 0;
                        var side = go.Spot.RightSide;
                        if (spot.equals(go.Spot.Top)) {
                            ang = 270;
                            side = go.Spot.TopSide;
                        } else if (spot.equals(go.Spot.Left)) {
                            ang = 180;
                            side = go.Spot.LeftSide;
                        } else if (spot.equals(go.Spot.Bottom)) {
                            ang = 90;
                            side = go.Spot.BottomSide;
                        }
                        if (!to) ang -= 180;
                        var setter = (e, shape) => {
                            e.handled = true;
                            e.diagram.model.commit(m => {

                                var link = shape.part.adornedPart;
                                m.set(link.data, (to ? "toSpot" : "fromSpot"), go.Spot.stringify(side));
                                // re-spread the connections of other links connected with the node
                                (to ? link.toNode : link.fromNode).invalidateConnectedLinks();
                            });
                        };
                        return $(go.Shape, {
                            alignment: spot,
                            alignmentFocus: spot.opposite(),
                            geometryString: "M0 0 M12 12 M12 6 L1 6 L4 4 M1 6 L4 8",
                            angle: ang,
                            background: "transparent",
                            mouseEnter: (e, shape) => shape.background = "dodgerblue",
                            mouseLeave: (e, shape) => shape.background = "transparent",
                            click: setter,
                            contextClick: setter

                        });
                    }

                    myDiagram.linkTemplate.contextMenu =
                        $("ContextMenu",
                            DarkColorButtons(),
                            StrokeOptionsButtons(),
                            $("ContextMenuButton",
                                $(go.Panel, "Horizontal",
                                    ArrowButton(0), ArrowButton(1), ArrowButton(2)
                                )
                            ),
                            $("ContextMenuButton",
                                $(go.Panel, "Horizontal",
                                    $(go.Panel, "Spot",
                                        AllSidesButton(false),
                                        SpotButton(go.Spot.Top, false), SpotButton(go.Spot.Left, false), SpotButton(go.Spot
                                            .Right,
                                            false), SpotButton(go.Spot.Bottom, false)
                                    ),
                                    $(go.Panel, "Spot", {
                                            margin: new go.Margin(0, 0, 0, 2)
                                        },
                                        AllSidesButton(true),
                                        SpotButton(go.Spot.Top, true), SpotButton(go.Spot.Left, true), SpotButton(go.Spot.Right,
                                            true), SpotButton(go.Spot.Bottom, true)
                                    )
                                )
                            ),
                        );

                    socket.on('mandarjsonusuario', (json) => {
                        myDiagram.model = go.Model.fromJson(json)
                    })


                    loading();
                }


                //guardar
                function autosave() {
                    console.log('autosave')

                    save();
                }
                setInterval('autosave()', 1800000);

                function save() {

                    //document.getElementById("mySavedModel").value = myDiagram.model.toJson();
                    saveInDB()
                    myDiagram.isModified = false;
                }

                function saveInDB() {
                    const id = "{{ $id }}"
                    const diagramajson = myDiagram.model.toJson()
                    data = {
                        'id': id,
                        'diagramajson': diagramajson
                    }
                    fetch('/api/saveDocument', {
                            headers: {
                                'Content-type': 'application/json'
                            },
                            method: 'POST',
                            body: JSON.stringify(data)
                        })
                        .then(response => response.json())
                        .catch(err => console.log("error"))

                }


                /*eliminar contenido de la pantalla */

                function eliminarDB() {
                    const id = "{{ $id }}"
                    const diagramajson = {

                        "class": "GraphLinksModel",
                        "nodeDataArray": [],
                        "linkDataArray": [],

                    }
                    data = {
                        'id': id,
                        'diagramajson': diagramajson
                    }
                    fetch('/api/deleteDocument', {
                            headers: {
                                'Content-type': 'application/json'
                            },
                            method: 'POST',
                            body: JSON.stringify(data)
                        })
                        .then(response => response.json())
                        .catch(err => console.log("error"))

                }

                function eliminar() {
                    eliminarDB();
                    load();
                }

                /****************************************************************************/
                //cargar
                function loading() {
                    const id = "{{ $id }}"
                    fetch('/api/loadDocument', {
                            headers: {
                                'Content-type': 'application/json',
                                'id': id
                            },
                            method: 'GET',
                        })
                        .then(response => response.json())
                        .then(respuesta => {
                            if (respuesta != null) {
                                myDiagram.model = go.Model.fromJson(respuesta)
                            }
                        })

                }

                function load() {
                    loading()
                    //myDiagram.model = go.Model.fromJson(document.getElementById("mySavedModel").value);
                }

                /*************************************************************************************************************/
                //png
                function myCallback(blob) {
                    var url = window.URL.createObjectURL(blob);
                    var filename = "Diagrama.jpg";
                    var a = document.createElement("a");
                    a.style = "display: none";
                    a.href = url;
                    a.download = filename;

                    // IE 11
                    if (window.navigator.msSaveBlob !== undefined) {
                        window.navigator.msSaveBlob(blob, filename);
                        return;
                    }

                    document.body.appendChild(a);
                    requestAnimationFrame(() => {
                        a.click();
                        window.URL.revokeObjectURL(url);
                        document.body.removeChild(a);
                    });
                }

                function convertirJPG() {
                    var blob = myDiagram.makeImageData({
                        background: "white",
                        returnType: "blob",
                        callback: myCallback
                    });
                }
                 /***********prueba**************************************************************************************************/
                //Imprimir
                function imprimir(){
                    window.print()
                }


                 /*************************************************************************************************************/
                //PDF
                function pdf() {
                    console.log('pdf');
                    const $elementoParaConvertir = document.getElementById('myDiagramDiv');
                    html2pdf()
                        .set({
                            margin: 1,

                            filename: 'Tudiagrama.pdf',
                            image: {
                                type: 'jpeg',
                                quality: 0.98
                            },
                            html2canvas: {
                                scale: 4, // a mayor escala, mejores graficos pero mas peso
                                letterRendering: true,
                            },
                            jsPDF: {
                                unit: "in",
                                format: "a3",
                                orientation: "portrait" //portrait=vertical || landscape = horizontal
                            }
                        })
                        .from($elementoParaConvertir)
                        .save()
                        .catch(err => console.log(err));
                }










                window.addEventListener('DOMContentLoaded', init);
            </script>

            <div id="sample">
                <div style="width: 100%; display: flex; justify-content: space-between">
                    <div id="myPaletteDiv" class="no-print"
                        style="width: 140px; margin-right: 2px; background-color: rgba(76, 172, 168, 0.529); border: 2px solid black; position: relative; -webkit-tap-highlight-color: rgba(255, 255, 255, 0);">
                        <canvas tabindex="0" width="128" height="772"
                            style="position: absolute; top: 0px; left: 0px; z-index: 2; user-select: none; touch-action: none; width: 103px; height: 618px;"></canvas>
                        <div style="position: absolute; overflow: auto; width: 103px; height: 618px; z-index: 1;">
                            <div style="position: absolute; width: 1px; height: 1px;"></div>
                        </div>
                    </div>
                    <div id="myDiagramDiv"
                        style="flex-grow: 1; height: 620px; border: 1px solid black; position: relative; -webkit-tap-highlight-color: rgb(255, 255, 255); background-color: rgba(255, 255, 255, 0.682);">
                        <canvas tabindex="0" width="1263" height="772"
                            style="position: absolute; top: 0px; left: 0px; z-index: 2; user-select: none; touch-action: none; width: 1011px; height: 618px; "></canvas>
                        <div style="position: absolute; overflow: auto; width: 1011px; height: 618px; z-index: 1; ">
                            <div style="position: absolute; width: 1px; height: 1px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </body>

    </html>
@endsection
