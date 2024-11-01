import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import ServerSideRender from '@wordpress/server-side-render';
import {
	PanelBody,
	SelectControl,
	RadioControl,
	ToggleControl
} from '@wordpress/components';

import {
	useBlockProps,
	InspectorControls
} from '@wordpress/block-editor';

registerBlockType( 'yummy/recipe-index', {
	apiVersion: 2,
	title: __('Recipe Index', 'yummy-recipes'),
	description: __('Choose to group recipes either by alphabets or by terms from a taxonomy.', 'yummy-recipes'),
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

		var taxonomyOptions = [];

		// Create an array of checkboxes.
		for (const property in filterOptions) {
			taxonomyOptions.push(
				{
					label: filterOptions[property],
					value: property,
				}
			)
		}

		return (
			<div { ...blockProps }>
				<ServerSideRender
					block="yummy/recipe-index"
					attributes={ attributes }
				/>
				<InspectorControls key="setting">
					<PanelBody title={ __('Settings', 'yummy-recipes') } initialOpen={ true }>
						<RadioControl
							label="Type"
							selected={ attributes.type }
							options={ [
								{ label: __('Alphabets', 'yummy-recipes'), value: 'az' },
								{ label: __('Taxonomies', 'yummy-recipes'), value: 'taxonomies' },
							] }
							onChange={ ( value ) =>
								setAttributes( { type: value } )
							}
						/>

						{ attributes.type === 'taxonomies' && (
							<SelectControl
								label={ __('Taxonomy', 'yummy-recipes') }
								value={ attributes.taxonomy }
								options={ taxonomyOptions }
								onChange={ ( value ) =>
									setAttributes( { taxonomy: value } )
								}
							/>
						)}

						<RadioControl
							label={ __('Style', 'yummy-recipes') }
							selected={ attributes.style }
							options={ [
								{ label: __('Cards', 'yummy-recipes'), value: 'cards' },
								{ label: __('List', 'yummy-recipes'), value: 'list' },
							] }
							onChange={ ( value ) =>
								setAttributes( { style: value } )
							}
						/>

						{ attributes.type === 'az' && (
							<ToggleControl
								label={ __('Show links to groups', 'yummy-recipes') }
								checked={ attributes.links }
								onChange={ ( value ) =>
									setAttributes( { links: Boolean(value) } )
								}
							/>
						)}
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
