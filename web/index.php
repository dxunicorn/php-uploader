<?

require "../src/App.php";

$app = new App;

?>
<!DOCTYPE html>
<html>
	<head>
		<title><?=$app->getTitle()?></title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Play&display=swap">
		<link rel="stylesheet" href="assets/css/general.css">
		<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/vue@2.6.11"></script>
		<script src="assets/js/main.js"></script>
	</head>
	<body>
		<?=$app->generateIconsBundle('assets/icons/')?>
		<div id="main" class="preloader">
			<div class="wrapper">
				<div class="reciever"
					v-bind:class="{ 'reciever--dragging': isDragging }"
					v-on:click="openFileDialog"
					v-on:drag.stop.prevent=""
					v-on:dragstart.stop.prevent=""
					v-on:dragend.stop.prevent=""
					v-on:dragover.stop.prevent=""
					v-on:dragenter.stop.prevent="startDrag"
					v-on:dragleave.stop.prevent="stopDrag"
					v-on:drop.stop.prevent="drop"
				>
					<icon class="reciever__icon" name="svg-cloud"></icon>
					<p class="reciever__message">{{ messages.prompt }}</p>
					<p class="reciever__limit">{{ messages.limits }} {{ maxFileSize | formatBytes }}</p>
				</div>
				<div class="cards">
					<card
						v-for="(item, index) in uploads"
						:key="index"
						ref="cards"
					></card>
				</div>
				<div class="footer">
					<p class="copyright">{{ messages.copyright }}&nbsp;&copy;&nbsp;{{ year }}
				</div>
			</div>
		</div>
		<script type="text/x-template" id="icon-template">
			<svg><use v-bind:href="href"></svg>
		</script>
		<script type="text/x-template" id="card-template">
			<div class="card" v-if="isShow">
				<div class="card__badge">
					<icon class="card__badge-icon" v-bind:name="icon"></icon>
				</div>
				<div class="card__body">
					<div class="card__title">
						<p class="card__file-name">{{ name | beautyTrim(30) }}</p>
						<p class="card__file-size">{{ size | formatBytes }}</p>
						<a class="card__btn-stop" href="#" v-on:click.stop.prevent="abort">
							<icon class="card__btn-icon" name="svg-close"></icon>
						</a>
					</div>
					<div class="card__footer">
						<template v-if="isDone">
							<template v-if="error">
								<icon class="card__status-icon card__status-icon--error" name="svg-cross"></icon>
								<p class="card__status">{{ error }}</p>
							</template>
							<template v-else>
								<icon class="card__status-icon" name="svg-check"></icon>
								<a class="card__btn-link" target="_blank" v-bind:title="messages.linktitle" v-bind:href="url">
									<icon class="card__btn-icon" name="svg-out"></icon>
								</a>
								<a class="card__btn-copy" href="javascript:;" v-bind:title="messages.copytitle" v-on:click.stop.prevent="urlToClipboard">
									<icon class="card__btn-icon" name="svg-chain"></icon>
								</a>
							</template>
						</template>
						<template v-else>
							<div class="card__progress-bar">
								<div class="card__progress-bar-inner" v-bind:style="{'width': progressBarWidth}" v-show="!isIdle"></div>
								<div class="card__progress-bar-inner card__progress-bar-inner--idle" v-show="isIdle"></div>
							</div>
						</template>
					</div>
				</div>
			</div>
		</script>
		<script>
			var app = new App(<?=$app->getJsParams()?>);
		</script>
	</body>
</html>