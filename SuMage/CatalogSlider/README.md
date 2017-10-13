# ProductSliderM2
A FREE Magento 2 module to create and manage product sliders.

- Built on top of fully responsive <a href="http://kenwheeler.github.io/slick/" target="_blank">Slick slider</a>
- Display products such as:
	-	**New Products**
	-	**Bestsellers Products**
	-	**Most Viewed Products**
        -       **Most Rated Products**
	-	**Featured Products**
	-	**Special Price Products**
        -       **Recently Viewed Products**


Or **mix and match** with the ones you want, or just make a **custom slider**. Choices. Good to have them.


# Features:
- ⏱ **Schedule slider** - Publish on specific date and time
- **Place anywhere** - Or exclude from an area like checkout and cart
- **Place easily** - using either XML, .phtml or a *widget*
- Display products in a default basic grid if needed
- 🎉 **Slider effects** - Choose any you like
- 🖖 **Pick and choose** - Add products manually <br/>
  For example: Your online channel just launched and Magento doesn't have a clue which your bestseller products are.
- **General settings** - One set of settings to rule them all
- **Per slider settings** - Exclude from the general rules

<br/>

**LIVE demo:**
<h2>There is no any demo for this slider.</h2>
<br/>

# Installation:
Composer
<h2>Step 1</h2>
In your Magento 2 root directory run
<strong>composer require sumage/catalogslider</strong>
bin/magento setup:upgrade

- <strong>or using Git</strong>:
	- create directory in the <strong>app/code/</strong>
	- clone module with: <strong>git clone  .</strong>

<h2>Step 2</h2>
- In magento root directory run following comands into the command line:
	- bin/magento module:enable SuMage_CatalogSlider
  	- bin/magento setup:upgrade

<h2>Step 3</h2>
- Login to Magento admin and enable extension at the JakeSharp => Settings => General => Enable
