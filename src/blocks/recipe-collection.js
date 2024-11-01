import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import ServerSideRender from '@wordpress/server-side-render';
import {
	PanelBody,
	Notice,
	SelectControl,
} from '@wordpress/components';

import {
	useBlockProps,
	InspectorControls
} from '@wordpress/block-editor';

registerBlockType( 'yummy/recipe-collection', {
	apiVersion: 2,
	title: __('Recipe Collection', 'yummy-recipes'),
	description: __('Shows recipes from a selected collection.', 'yummy-recipes'),
	category: 'yummy-recipes',
	icon: 'food',
	keywords: [
		__( 'yummy' ),
		__( 'recipes' ),
	],

	edit: ( { attributes, setAttributes } ) => {
		const blockProps = useBlockProps();

		// Get collection terms from the variable set in wp_localize_script.
		const collections = yummyBlocks.recipeListCollections;

		var collectionOptions = [];

		// Create an array of options.
		collectionOptions.push( { label: '---', value: 0 } );
		collections.forEach(function (term) {
			collectionOptions.push( {
				label: term.name.replace(/&amp;/g, '&'),
				value: term.term_id
			} );
		});

		return (
			<div { ...blockProps }>
				<ServerSideRender
					block="yummy/recipe-collection"
					attributes={ attributes }
				/>
				<InspectorControls key="setting">
					<PanelBody title={__('Settings', 'yummy-recipes')} initialOpen={ true }>
						{ collectionOptions.length < 2 && (
							<Notice status="warning" isDismissible={false}>{__('You have not created any collections yet, or there are no recipes in your collections.', 'yummy-recipes')}</Notice>
						)}

						{ collectionOptions.length > 1 && (
							<SelectControl
								label={__('Collection', 'yummy-recipes')}
								value={ attributes.term_id }
								options={ collectionOptions }
								onChange={ ( value ) =>
									setAttributes( { term_id: Number( value ) } )
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
