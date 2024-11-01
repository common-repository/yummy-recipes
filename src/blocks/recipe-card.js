import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { Button, ButtonGroup, TextControl, Card, CardBody, CardMedia, CardDivider, Icon, Tooltip } from '@wordpress/components';
import { Fragment } from '@wordpress/element';

import {
	useBlockProps,
	RichText,
	InnerBlocks,
	MediaUpload
} from '@wordpress/block-editor';

registerBlockType( 'yummy/recipe-card', {
	apiVersion: 2,
	title: __('Recipe Card'),
	description: __('Shows a recipe card.', 'yummy-recipes'),
	category: 'yummy-recipes',
	icon: 'food',
	keywords: [
		__( 'yummy' ),
		__( 'recipes' ),
		__( 'card' ),
	],

	edit: ( { attributes, setAttributes } ) => {
		const blockProps = useBlockProps();

		const {
			mediaID, mediaURL, ingredients
		} = attributes;

		let IngredientsObject = JSON.parse(ingredients);

		const INNERBLOCKS_ALLOWED_BLOCKS = [ 'core/heading', 'core/list', 'core/paragraph', 'core/image' ];

		const INNERBLOCKS_TEMPLATE = [
			[ 'core/list', { ordered: true, placeholder: __('Write instructions for the recipe...', 'yummy-recipes') } ],
		];

		// Get nutritionOptions from the variable set in wp_localize_script.
		const nutritionOptions = yummyBlocks.recipeCardNutritionOptions;

		const updateIngredientsValue = (newValue) => {

			const copy = [ ...IngredientsObject ];
			IngredientsObject = copy;

			var json = JSON.stringify(newValue);
			setAttributes({ingredients: json});
		}

		const handleAddIngredient = () => {
			const copy = [ ...IngredientsObject ];
			copy.push( {
				amount: '',
				unit: '',
				name: '',
			} );
			updateIngredientsValue( copy );
		};

		const handleRemoveIngredient = ( index ) => {
			const copy = [ ...IngredientsObject ];
			if (copy.length < 2) {
				return;
			}
			copy.splice( index, 1 );
			updateIngredientsValue( copy );
		};

		const handleIngredientNameChange = ( name, index ) => {
			const copy = [ ...IngredientsObject ];
			copy[ index ].name = name;
			updateIngredientsValue( copy );
		};

		const handleIngredientAmountChange = ( amount, index ) => {
			const copy = [ ...IngredientsObject ];
			copy[ index ].amount = amount;
			updateIngredientsValue( copy );
		};

		const handleIngredientUnitChange = ( unit, index ) => {
			const copy = [ ...IngredientsObject ];
			copy[ index ].unit = unit;
			updateIngredientsValue( copy );
		};

		const handleIngredientTitleChange = ( value, index ) => {
			const copy = [ ...IngredientsObject ];
			copy[ index ].title = value;
			updateIngredientsValue( copy );
		};

		const handleAddIngredientTitle = () => {
			const copy = [ ...IngredientsObject ];
			copy.push( {
				title: '',
			} );
			updateIngredientsValue( copy );
		};

		const handleTableRowMove = ( index, direction ) => {
			var copy = [ ...IngredientsObject ];

			// Can't move any higher.
			if ('up' === direction && index === 0) {
				return;
			}

			// Can't move any lower.
			if ('down' === direction && index === copy.length - 1) {
				return;
			}

			var new_index = ( 'up' === direction ) ? index - 1 : index + 1;

			if (new_index >= copy.length) {
				var k = new_index - copy.length + 1;
				while (k--) {
					copy.push(undefined);
				}
			}
			copy.splice(new_index, 0, copy.splice(index, 1)[0]);
			updateIngredientsValue( copy );
		};

		const handleNutritionChange = ( value, structured_data_name ) => {
			// Create a copy of the attribute object.
			var copy = Object.assign({}, attributes.nutrition);
			copy[structured_data_name] = value;
			setAttributes({nutrition: copy});
		};

		const onSelectImage = ( media ) => {
			setAttributes( {
				mediaURL: media.url,
				mediaID: media.id,
			} );
		};

		const onRemoveImage = () => {
			setAttributes( {
				mediaURL: '',
				mediaID: '',
			} );
		};

		let ingredientsFields;

		if ( typeof IngredientsObject !== 'undefined' && IngredientsObject.length ) {
			ingredientsFields = IngredientsObject.map( ( ingredient, index ) => {

				return <Fragment key={ index }>
					<tr>
						{ ingredient.name !== undefined && (
							<>
							<td>
								<TextControl
									label={ __( 'Amount', 'yummy-recipes' ) }
									hideLabelFromVision={true}
									value={ IngredientsObject[ index ].amount }
									onChange={ ( amount ) => handleIngredientAmountChange( amount, index ) }
								/>
							</td>
							<td>
								<TextControl
									label={ __( 'Unit', 'yummy-recipes' ) }
									hideLabelFromVision={true}
									value={ IngredientsObject[ index ].unit }
									onChange={ ( unit ) => handleIngredientUnitChange( unit, index ) }
								/>
							</td>
							<td>
								<TextControl
									label={ __( 'Name', 'yummy-recipes' ) }
									hideLabelFromVision={true}
									value={ IngredientsObject[ index ].name }
									onChange={ ( name ) => handleIngredientNameChange( name, index ) }
								/>
							</td>
							</>
						) }
						{ ingredient.title !== undefined && (
								<td colSpan="3" style={ {paddingTop: '8px'} }>
									<TextControl
										label={ __( 'Title', 'yummy-recipes' ) }
										value={ IngredientsObject[ index ].title }
										onChange={ ( title ) => handleIngredientTitleChange( title, index ) }
									/>
								</td>
						) }

						<td style={ {verticalAlign: 'bottom', minWidth: '160px'} }>
							<ButtonGroup>
								<Button
									isSmall={true}
									isDestructive={true}
									icon={ <Icon icon="trash" /> }
									label={ __( 'Delete', 'yummy-recipes' ) }
									onClick={ () => handleRemoveIngredient( index ) }
								/>
								<Button
									isSmall={true}
									icon={ <Icon icon="arrow-up-alt2" /> }
									label={ __( 'Move up', 'yummy-recipes' ) }
									onClick={ () => handleTableRowMove( index, 'up' ) }
								/>
								<Button
									isSmall={true}
									icon={ <Icon icon="arrow-down-alt2" /> }
									label={ __( 'Move down', 'yummy-recipes' ) }
									onClick={ () => handleTableRowMove( index, 'down' ) }
								/>
							</ButtonGroup>
						</td>
					</tr>
				</Fragment>;
			} );
		}

		let nutritionFields;

		nutritionFields = nutritionOptions.map( ( item, index ) => {

			return <Fragment key={ index }>
				<TextControl
					label={ item.name }
					value={ attributes.nutrition[item.structured_data_name] }
					onChange={ ( value ) => handleNutritionChange( value, item.structured_data_name ) }
				/>
			</Fragment>;
		} );

		return [
			<div { ...blockProps } key="yummy-recipe-card">
				<Card size="small">
					{ mediaID && (
						<CardMedia>
							<img
								src={ mediaURL }
							/>
						</CardMedia>
					) }

					<div className="recipe-image">
						<MediaUpload
							onSelect={ onSelectImage }
							allowedTypes="image"
							value={ mediaID }
							render={ ( { open } ) => (
								<Button
									style={ {marginRight: '8px' } }
									variant="secondary"
									onClick={ open }
								>
									{ ! mediaID ? (
										__( 'Select or Upload Image', 'yummy-recipes' )
									) :
										__( 'Change Image', 'yummy-recipes' )
									}
								</Button>
							) }
						/>

						{ mediaID &&
							<Button
								variant='tertiary'
								onClick={ onRemoveImage }
							>{ __('Remove image', 'yummy-recipes') }
							</Button>
						}
					</div>

					<CardBody>
						<div className="wp-block-columns">
							<div className="wp-block-column">
								<TextControl
									label={ __('Prep time', 'yummy-recipes') }
									value={ attributes.prepTime || '' }
									type="number"
									help={ __('Minutes', 'yummy-recipes') }
									onChange={ ( value ) =>
										setAttributes( { prepTime: value } )
									}
									/>
							</div>
							<div className="wp-block-column">
								<TextControl
									label={ __('Cook time', 'yummy-recipes') }
									value={ attributes.cookTime || '' }
									type="number"
									help={ __('Minutes', 'yummy-recipes') }
									onChange={ ( value ) =>
										setAttributes( { cookTime: value } )
									}
								/>
							</div>
							<div className="wp-block-column">
								<TextControl
									label={ __('Yield', 'yummy-recipes') }
									value={ attributes.yield || '' }
									onChange={ ( value ) =>
										setAttributes( { yield: value } )
									}
								/>

								<Tooltip text={ __( 'The quantity produced by the recipe. For example: number of people served, or number of servings.', 'yummy-recipes' ) }>
									<Icon icon={ <Icon icon="info-outline" /> } style={ {color: '#757575'} } />
								</Tooltip>
							</div>
						</div>

						<CardDivider />

						<h3>{ __( 'Description', 'yummy-recipes' ) }</h3>

						<RichText
							tagName="p"
							placeholder={ __('Write a short description of the recipe...', 'yummy-recipes') }
							value={ attributes.description }
							onChange={ ( value ) =>
								setAttributes( { description: value } )
							}
							className="yummy-description"
						/>

						<CardDivider />

						<h3>{ __( 'Ingredients', 'yummy-recipes' ) }</h3>

						<table className="yummy-table-ingredients">
							<thead>
								<tr>
									<td style={ {width: '15%' } }>Amount</td>
									<td>Unit</td>
									<td style={ {width: '40%' } }>Name</td>
								</tr>
							</thead>

							<tbody>
								{ ingredientsFields }
							</tbody>
						</table>

						<Button
							style={ {marginRight: '8px' } }
							variant="secondary"
							onClick={ handleAddIngredient.bind( this ) }
							>
							{ __( 'Add Ingredient', 'yummy-recipes' ) }
						</Button>

						<Button
							variant="secondary"
							onClick={ handleAddIngredientTitle.bind( this ) }
							>
							{ __( 'Add Title', 'yummy-recipes' ) }
						</Button>

						<div className="yummy-instructions">
							<h3>{ __( 'Instructions', 'yummy-recipes' ) }</h3>

							<InnerBlocks
								template={ INNERBLOCKS_TEMPLATE }
								allowedBlocks={ INNERBLOCKS_ALLOWED_BLOCKS }
							/>
						</div>

						<h3>{ __( 'Nutrition Facts', 'yummy-recipes' ) }</h3>

						{ nutritionFields }

						<h3>{ __( 'Video URL', 'yummy-recipes' ) }</h3>

						<TextControl
							label={ __('Video URL', 'yummy-recipes') }
							hideLabelFromVision={true}
							value={ attributes.video_url || '' }
							onChange={ ( value ) =>
								setAttributes( { video_url: value } )
							}
						/>
					</CardBody>
				</Card>
			</div>
		];
	},
	// We're going to be rendering in PHP, so save() can just return null.
	save: function() {
		return <InnerBlocks.Content />;
	}
} );
