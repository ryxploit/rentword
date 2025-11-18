/**
 * RentWord Gutenberg Blocks
 * 
 * @package RentWord
 */

(function(blocks, element, components, editor) {
    var el = element.createElement;
    var registerBlockType = blocks.registerBlockType;
    var InspectorControls = editor.InspectorControls;
    var PanelBody = components.PanelBody;
    var RangeControl = components.RangeControl;
    var ToggleControl = components.ToggleControl;
    var TextControl = components.TextControl;

    /**
     * Properties Grid Block
     */
    registerBlockType('rentword/properties-grid', {
        title: 'Properties Grid',
        icon: 'grid-view',
        category: 'rentword',
        attributes: {
            limit: {
                type: 'number',
                default: 6
            },
            columns: {
                type: 'number',
                default: 3
            },
            featured: {
                type: 'boolean',
                default: false
            }
        },
        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            return [
                el(InspectorControls, {},
                    el(PanelBody, { title: 'Settings' },
                        el(RangeControl, {
                            label: 'Number of Properties',
                            value: attributes.limit,
                            onChange: function(value) {
                                setAttributes({ limit: value });
                            },
                            min: 1,
                            max: 50
                        }),
                        el(RangeControl, {
                            label: 'Columns',
                            value: attributes.columns,
                            onChange: function(value) {
                                setAttributes({ columns: value });
                            },
                            min: 1,
                            max: 4
                        }),
                        el(ToggleControl, {
                            label: 'Show Only Featured',
                            checked: attributes.featured,
                            onChange: function(value) {
                                setAttributes({ featured: value });
                            }
                        })
                    )
                ),
                el('div', { className: props.className },
                    el('div', { style: { padding: '20px', background: '#f0f0f0', textAlign: 'center' } },
                        el('p', {}, 'RentWord Properties Grid'),
                        el('p', { style: { fontSize: '14px', color: '#666' } },
                            'Showing ' + attributes.limit + ' properties in ' + attributes.columns + ' columns' +
                            (attributes.featured ? ' (Featured only)' : '')
                        )
                    )
                )
            ];
        },
        save: function() {
            return null; // Rendered server-side
        }
    });

    /**
     * Featured Slider Block
     */
    registerBlockType('rentword/featured-slider', {
        title: 'Featured Properties Slider',
        icon: 'images-alt2',
        category: 'rentword',
        attributes: {
            limit: {
                type: 'number',
                default: 10
            }
        },
        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            return [
                el(InspectorControls, {},
                    el(PanelBody, { title: 'Settings' },
                        el(RangeControl, {
                            label: 'Number of Properties',
                            value: attributes.limit,
                            onChange: function(value) {
                                setAttributes({ limit: value });
                            },
                            min: 1,
                            max: 20
                        })
                    )
                ),
                el('div', { className: props.className },
                    el('div', { style: { padding: '20px', background: '#f0f0f0', textAlign: 'center' } },
                        el('p', {}, 'RentWord Featured Properties Slider'),
                        el('p', { style: { fontSize: '14px', color: '#666' } },
                            'Showing ' + attributes.limit + ' featured properties'
                        )
                    )
                )
            ];
        },
        save: function() {
            return null; // Rendered server-side
        }
    });

    /**
     * Property Search Block
     */
    registerBlockType('rentword/property-search', {
        title: 'Property Search Form',
        icon: 'search',
        category: 'rentword',
        edit: function(props) {
            return el('div', { className: props.className },
                el('div', { style: { padding: '20px', background: '#f0f0f0', textAlign: 'center' } },
                    el('p', {}, 'RentWord Property Search Form'),
                    el('p', { style: { fontSize: '14px', color: '#666' } },
                        'Advanced search form with filters'
                    )
                )
            );
        },
        save: function() {
            return null; // Rendered server-side
        }
    });

    /**
     * Properties Map Block
     */
    registerBlockType('rentword/properties-map', {
        title: 'Properties Map',
        icon: 'location-alt',
        category: 'rentword',
        attributes: {
            height: {
                type: 'string',
                default: '500px'
            },
            zoom: {
                type: 'number',
                default: 12
            }
        },
        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            return [
                el(InspectorControls, {},
                    el(PanelBody, { title: 'Settings' },
                        el(TextControl, {
                            label: 'Height',
                            value: attributes.height,
                            onChange: function(value) {
                                setAttributes({ height: value });
                            },
                            help: 'e.g., 500px or 50vh'
                        }),
                        el(RangeControl, {
                            label: 'Default Zoom Level',
                            value: attributes.zoom,
                            onChange: function(value) {
                                setAttributes({ zoom: value });
                            },
                            min: 1,
                            max: 20
                        })
                    )
                ),
                el('div', { className: props.className },
                    el('div', {
                        style: {
                            padding: '20px',
                            background: '#f0f0f0',
                            textAlign: 'center',
                            height: attributes.height,
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'center'
                        }
                    },
                        el('div', {},
                            el('p', {}, 'RentWord Properties Map'),
                            el('p', { style: { fontSize: '14px', color: '#666' } },
                                'Map with all properties (Height: ' + attributes.height + ', Zoom: ' + attributes.zoom + ')'
                            )
                        )
                    )
                )
            ];
        },
        save: function() {
            return null; // Rendered server-side
        }
    });

})(
    window.wp.blocks,
    window.wp.element,
    window.wp.components,
    window.wp.blockEditor || window.wp.editor
);
