<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>TLMind</title>
		<!-- JQUERY CORE -->
		<script type="text/javascript" src="jquery-1.4.4.js"></script>
		<script type="text/javascript" src="jquery.form.js"></script>
		<script type="text/javascript" src="popup.js"></script>
		<link rel="stylesheet" type="text/css" href="css/popup.css">
	</head>
	<body>
		<h1>TLMind</h1>
		<p>Upload a freemind file (.mm) in order to convert it in TestLink xml file to import.</p>
		<p>
			<form id="uploadForm" action="upload.php" method="POST" enctype="multipart/form-data">
				 <input type="hidden" name="MAX_FILE_SIZE" value="100000">
				<table>
					<tr>
						<td>File :</td>
						<td><input type="file" name="mm"></td>
					</tr>
					<tr>
						<td>Tests :</td>
						<td><input type=radio name="type" value="tests" selected="selected"></td>
					</tr>
					<tr>
						<td>Requirements :</td>
						<td><input type=radio name="type" value="requirements"></td>
					</tr>
					<tr colspan=2>
						<td><input type="submit" name="envoyer" value="Upload"></td>
					</tr>
					</table>
			</form>
		</p>
		<footer><small>Developped by <a href='faure.thomas@gmail.com'>Thomas Faur&eacute;</a> - &copy; 2011</small></footer>
	</body>
</html>
