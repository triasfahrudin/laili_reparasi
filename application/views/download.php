<html lang="en"><head>
		<title></title>
	</head>
	<body>
		<progress id="progress" value="0"></progress>
		<button id="button">Download</button>
		<span id="display"></span>
    <script>
		var progressBar = document.getElementById("progress"),
		loadBtn = document.getElementById("button"),
		display = document.getElementById("display");

		function download() {
			var xhr = new XMLHttpRequest();
			xhr.open("GET", "https://file.ac/bUlqIWEO128/tb_content.sql?download=true");
			xhr.responseType = "text";
			xhr.onprogress = function(e) {
				if (e.lengthComputable) {
					progressBar.max = e.total;
					progressBar.value = e.loaded;
					display.innerText = Math.floor((e.loaded / e.total) * 100) + '%';
				}
			};
			xhr.onloadstart = function(e) {
				progressBar.value = 0;
				display.innerText = '0%';
			};
			xhr.onloadend = function(e) {
				progressBar.value = e.loaded;
				loadBtn.disabled = false;
				loadBtn.innerHTML = 'Download';
			};
			xhr.onload = function (e) {
				if (this.status == 200) {
					console.log('Download complete');
				}
			};
			xhr.send(null);
		}

		loadBtn.addEventListener("click", function(e) {
			this.disabled = true;
			this.innerHTML = "Downloading...";
			download();
		});
		</script>
