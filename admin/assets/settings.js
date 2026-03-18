/**
 * Settings Page JavaScript
 */

// Tab switching handled by inline JavaScript in content
document.addEventListener('DOMContentLoaded', function () {
	const btn = document.getElementById('changePasswordBtn');
	if (!btn) return;

	const currentInput = document.getElementById('current_password');
	const newInput = document.getElementById('new_password');
	const confirmInput = document.getElementById('confirm_password');
	const msgDiv = document.getElementById('passwordMessage');

	btn.addEventListener('click', async function () {
		if (!currentInput || !newInput || !confirmInput) return;

		const current = currentInput.value.trim();
		const nw = newInput.value.trim();
		const confirm = confirmInput.value.trim();

		msgDiv.textContent = '';
		btn.disabled = true;
		btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Updating...';

		if (!current || !nw || !confirm) {
			msgDiv.textContent = 'Please fill all fields.';
			btn.disabled = false;
			btn.innerHTML = '<i class="fas fa-key mr-2"></i> Update Password';
			return;
		}

		if (nw.length < 8) {
			msgDiv.textContent = 'New password must be at least 8 characters.';
			btn.disabled = false;
			btn.innerHTML = '<i class="fas fa-key mr-2"></i> Update Password';
			return;
		}

		if (nw !== confirm) {
			msgDiv.textContent = 'New password and confirmation do not match.';
			btn.disabled = false;
			btn.innerHTML = '<i class="fas fa-key mr-2"></i> Update Password';
			return;
		}

		try {
			const formData = new FormData();
			formData.append('current_password', current);
			formData.append('new_password', nw);
			formData.append('confirm_password', confirm);

			const res = await fetch('./api/change_password.php', {
				method: 'POST',
				body: formData
			});

			const data = await res.json();

			if (data.success) {
				msgDiv.style.color = 'green';
				msgDiv.textContent = data.message || 'Password updated';
				currentInput.value = '';
				newInput.value = '';
				confirmInput.value = '';
			} else {
				msgDiv.style.color = 'red';
				msgDiv.textContent = data.message || 'Failed to update password';
			}
		} catch (err) {
			msgDiv.style.color = 'red';
			msgDiv.textContent = 'Network error: ' + (err.message || err);
		} finally {
			btn.disabled = false;
			btn.innerHTML = '<i class="fas fa-key mr-2"></i> Update Password';
		}
	});
});
