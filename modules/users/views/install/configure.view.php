<script language="JavaScript" id="ajax-callback">
if (typeof(UsersConfigure) == 'undefined') {
	$.getScript(Fabriq.base_path() + 'modules/users/javascripts/users-configure.js', function() {
		UsersConfigure.init();
	});
} else {
	UsersConfigure.init();
}
</script>
<div style="padding: 2px;">
	<label for="useCustom">
		<input type="checkbox" name="useCustom" id="useCustom" value="1" /> Use custom user functionality
	</label>
</div>
<fieldset id="users-module-configuration" style="width: 100%;">
	<legend>Custom user functionality settings</legend>
	<div style="padding: 2px;">
		<label for="customTable">
			Users database table: <input type="text" size="50" maxlength="50" name="customTable" id="customTable" />
		</label>
	</div>
	<div style="padding: 2px;">
		<label for="customIdField">
			Users ID field: <input type="text" size="50" maxlength="50" name="customIdField" id="customIdField" />
		</label>
	</div>
	<div style="padding: 2px;">
		<label for="customDisplayField">
			Display name field: <input type="text" size="50" maxlength="50" name="customDisplayField" id="customDisplayField" />
		</label>
	</div>
	<div style="padding: 2px;">
		<label for="customController">
			Custom controller: <input type="text" size="50" maxlength="50" name="customController" id="customController" />
		</label>
	</div>
	<div style="padding: 2px;">
		<label for="customLoginAction">
			Login action: <input type="text" size="50" maxlength="50" name="customLoginAction" id="customLoginAction" />
		</label>
	</div>
	<div style="padding: 2px;">
		<label for="customLogoutAction">
			Logout action: <input type="text" size="50" maxlength="50" name="customLogoutAction" id="customLogoutAction" />
		</label>
	</div>
	<div style="padding: 2px;">
		<label for="customCreateAction">
			Create action: <input type="text" size="50" maxlength="50" name="customCreateAction" id="customCreateAction" />
		</label>
	</div>
	<div style="padding: 2px;">
		<label for="customUpdateAction">
			Update action: <input type="text" size="50" maxlength="50" name="customUpdateAction" id="customUpdateAction" />
		</label>
	</div>
	<div style="padding: 2px;">
		<label for="customBanAction">
			Ban action: <input type="text" size="50" maxlength="50" name="customBanAction" id="customBanAction" />
		</label>
	</div>
	<div style="padding: 2px;">
		<label for="customEnableAction">
			Enable action: <input type="text" size="50" maxlength="50" name="customEnableAction" id="customEnableAction" />
		</label>
	</div>
	<div style="padding: 2px;">
		<label for="customForgotPasswordAction">
			Forgot password action: <input type="text" size="50" maxlength="50" name="customForgotPasswordAction" id="customForgotPasswordAction" />
		</label>
	</div>
	<div style="padding: 2px;">
		<label for="customRegisterAction">
			Register action: <input type="text" size="50" maxlength="50" name="customRegisterAction" id="customRegisterAction" />
		</label>
	</div>
	<div style="padding: 2px;">
		<label for="customIsLoggedInAction">
			Is logged in: <input type="text" size="50" maxlength="50" name="customIsLoggedInAction" id="customIsLoggedInAction" />
		</label>
	</div>
</fieldset>
<div style="padding: 2px;">
	<button id="usersSaveConfig" name="usersSaveConfig" onclick="UsersConfigure.saveConfiguration();">Save settings</button>
</div>
