import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import ServerSideRender from '@wordpress/server-side-render';
import {
	PanelBody,
	CheckboxControl,
	RadioControl,
	ToggleControl
} from '@wordpress/components';

import {
	useBlockProps,
	InspectorControls
} from '@wordpress/block-editor';

registerBlockType( 'yummy/term-index', {
	apiVersion: 2,
	title: __('Term Index', 'yummy-recipes'),
	description: __('Shows recipe term index.', 'yummy-recipes'),
	category: 'yummy-recipes',
	icon: 'food',
	keywords: [
		__( 'yummy' ),
		__( 'recipes' ),
	],

	edit: ( { attributes, setAttributes } ) => {
		const blockProps = useBlockProps();

		// Get object taxonomies from the variable set in wp_localize_script.
		const filterOptions = yummyBlocks.taxonomyFilterOptions;

		const onTaxonomyChange = (property) => {
			// Create a copy of the attribute as it can't be modified directly.
			var copy = attributes.taxonomies.slice( 0, attributes.taxonomies.length );

			if (copy.includes(property)) {
				var index = copy.indexOf(property);
				if (index !== -1) {
					copy.splice(index, 1);
				}
			} else {
				copy.push(property);
			}

			setAttributes({taxonomies: copy});
		}

		var checkboxes = [];

		// Create an array of checkboxes.
		for (const property in filterOptions) {
			checkboxes.push(
				<CheckboxControl
					key={property}
					label={filterOptions[property]}
					checked={ attributes.taxonomies.includes(property) }
					onChange={ () => {
						onTaxonomyChange(property)
					}}
				/>
			)
		}

		return (
			<div { ...blockProps }>
				<ServerSideRender
					block="yummy/term-index"
					attributes={ attributes }
				/>
				<InspectorControls key="setting">
					<PanelBody title={__('Options', 'yummy-recipes')} initialOpen={ true }>
						<h3>{__('Displayed taxonomies', 'yummy-recipes')}</h3>

						{ checkboxes }

						<RadioControl
							label={__('Style', 'yummy-recipes')}
							selected={ attributes.style }
							options={ [
								{ label: __('Cards', 'yummy-recipes'), value: 'cards' },
								{ label: __('List', 'yummy-recipes'), value: 'list' },
							] }
							onChange={ ( value ) =>
								setAttributes( { style: value } )
							}
						/>

						<ToggleControl
							label={__('Show recipe count', 'yummy-recipes')}
							checked={ attributes.show_count }
							onChange={ ( value ) =>
								setAttributes( { show_count: value } )
							}
						/>
					</PanelBody>
				</InspectorControls>
			</div>
		);
	},

	// We're going to be rendering in PHP, so save() can just return null.
	save: function() {
		return null;
	},
} );
