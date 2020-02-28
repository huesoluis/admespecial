<!DOCTYPE html>
<html lang="en">
	<head>
		<title>EasyAutocomplete json example</title>
		<link href="../css/easy-autocomplete.min.css" rel="stylesheet" type="text/css">
    <script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/easy-autocomplete/1.3.5/jquery.easy-autocomplete.min.js" type="text/javascript" ></script>
	</head>
	<body>

		<h1>EasyAutocomplete - json example</h1>

		<input id="data-json" />

		<script>

		var options = {
			url: "resources/countries.json",

			getValue: "name",

			list: {
				match: {
					enabled: true
				}
			}
		};

		$("#data-json").easyAutocomplete(options);

		</script>

	</body>

</html>

