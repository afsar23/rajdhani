<?php
namespace Afsar\wtk;
use Afsar\wtk;

/*
Mohammed Afsar
Single Page Application for the Plugin Application
Supported by a customer API registration routes
Uses the w2ui library
*/

defined('ABSPATH') or die("Cannot access pages directly.");   

######################################################################################


// invoked by shortcode in pubControler script
function wtk_infinitescroll() {
	
	?>

	<style>
		.facts {
			display: flex;
			flex-direction: column;
			justify-content: center;
		}

		blockfact {
			margin-bottom: 25px;
		}

		.fact {
			position: relative;
			font-size: 20px;
			line-height: 1.7em;
			word-break: break-word;


		}

		.fact footer {
			font-size: 0.6em;
			font-weight: 700;
			color: #d3d3cf;
			text-align: right;
		}

		.fact footer:before {
			content: '\2015';
		}

		.fact:after {
			content: '\201D';
			position: absolute;
			top: 0.28em;
			right: 0;
			font-size: 6em;
			font-style: italic;
			color: #e7e6e4;
			z-index: -1;
		}

		/* loader */

		.loader {
			display: inline-block;
			position: relative;
			width: 80px;
			height: 80px;
			opacity: 0;

		}


		.loader.show {
			opacity: 1;
		}

		.loader div {
			display: inline-block;
			position: absolute;
			left: 8px;
			width: 16px;
			background: #f4f4f4;
			animation: loader 1.2s cubic-bezier(0, 0.5, 0.5, 1) infinite;
		}

		.loader div:nth-child(1) {
			left: 8px;
			animation-delay: -0.24s;
		}

		.loader div:nth-child(2) {
			left: 32px;
			animation-delay: -0.12s;
		}

		.loader div:nth-child(3) {
			left: 56px;
			animation-delay: 0;
		}

		@keyframes loader {
			0% {
				top: 8px;
				height: 64px;
			}

			50%,
			100% {
				top: 24px;
				height: 32px;
			}
		}	
	
	</style>
	
	
	<div class="container">
		<h1>Fun Facts about Cats</h1>
		<div class="facts">
		</div>
		<div class="loader">
		<div></div>
		<div></div>
		<div></div>
		</div>
	</div>


	<script>
	
		let currentPage = 1;
		const limit = 3;
		let total = 0;
		const factsEl = document.querySelector('.facts');
		const loader = document.querySelector('.loader');
		
		const getfacts = async (page, limit) => {
			const API_URL = `https://catfact.ninja/facts?page=${page}&limit=${limit}`;
			const response = await fetch(API_URL);
			// handle 404
			if (!response.ok) {
				throw new Error(`An error occurred: ${response.status}`);
			}
			return await response.json();
		}
		const showfacts = (facts) => {
			facts.forEach(fact => {
				const factEl = document.createElement('blockfact');
				factEl.classList.add('fact');
				factEl.innerHTML = `
					${fact.fact}
				`;
				factsEl.appendChild(factEl);  
			});
		};
		const hideLoader = () => {
			loader.classList.remove('show');
		};

		const showLoader = () => {
			loader.classList.add('show');
		};
		const hasMorefacts = (page, limit, total) => {
			const startIndex = (page - 1) * limit + 1;
			return total === 0 || startIndex < total;
		};

		const loadfacts = async (page, limit) => {
			// show the loader
			showLoader();
			try {
				// if having more facts to fetch
				if (hasMorefacts(page, limit, total)) {
					// call the API to get facts
					const response = await getfacts(page, limit);
					// show facts
					showfacts(response.data);
					// update the total
					total = response.total;
				}
			} catch (error) {
				console.log(error.message);
			} finally {
				hideLoader();
			}
		};
		window.addEventListener('scroll', () => {
			const {
				scrollTop,
				scrollHeight,
				clientHeight
			} = document.documentElement;

			if (scrollTop + clientHeight >= scrollHeight - 5 &&
				hasMorefacts(currentPage, limit, total)) {
				currentPage++;
				loadfacts(currentPage, limit);
			}
		}, {
			passive: true
		});
		loadfacts(currentPage, limit);
		
	</script>

	<?php
	
	

}  // end 


