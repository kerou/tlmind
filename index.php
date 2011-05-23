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
		<hr/>
		<p>Upload a freemind file (.mm) in order to convert it in TestLink (.xml) file to import.</p>
		<p><img src='images/mmtoxml.png'/></p>
		<p>
			<form id="uploadForm" action="upload.php" method="POST" enctype="multipart/form-data">
				 <input type="hidden" name="MAX_FILE_SIZE" value="1000000">
				<table>
					<tr>
						<td>MM File :</td>
						<td><input type="file" name="mm"></td>
					</tr>
					<tr>
						<td>Prefix :</td>
						<td><input type=text name="prefix" value="PRE"></td>
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
		<hr/>
		<p>Upload a TestLink file (.xml) in order to convert it in Freemind (.mm) file to import.</p>
		<p><img src='images/xmltomm.png'/></p>
		<p>
			<form id="uploadFormXmlToMm" action="uploadXmlToMm.php" method="POST" enctype="multipart/form-data">
				 <input type="hidden" name="MAX_FILE_SIZE" value="1000000">
				<table>
					<tr>
						<td>XML File :</td>
						<td><input type="file" name="xmlf"></td>
					</tr>
					<tr>
						<td>Prefix :</td>
						<td><input type=text name="prefix" value="PRE"></td>
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
		<hr/>
		<footer><small>Developped by <a href='mailto:faure.thomas@gmail.com'>Thomas Faur&eacute;</a> - &copy; 2011</small></footer>
	</body>
</html>
