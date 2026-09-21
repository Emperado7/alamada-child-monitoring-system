package com.alamada.childmonitor.activities;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import com.alamada.childmonitor.databinding.ActivityLoginBinding;
import com.alamada.childmonitor.models.AuthResponse;
import com.alamada.childmonitor.network.ApiClient;
import com.alamada.childmonitor.utils.SessionManager;

import java.util.HashMap;
import java.util.Map;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class LoginActivity extends AppCompatActivity {

    private ActivityLoginBinding binding;
    private SessionManager session;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivityLoginBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        // Hide the default action bar — we have a custom header
        if (getSupportActionBar() != null) getSupportActionBar().hide();

        session = new SessionManager(this);

        binding.btnLogin.setOnClickListener(v -> attemptLogin());

        binding.tvForgotPassword.setOnClickListener(v ->
            Toast.makeText(this,
                "Please contact the learning center administrator to reset your password.",
                Toast.LENGTH_LONG).show()
        );
    }

    private void attemptLogin() {
        String email    = binding.etEmail.getText().toString().trim();
        String password = binding.etPassword.getText().toString().trim();

        if (email.isEmpty()) {
            binding.etEmail.setError("Email is required");
            binding.etEmail.requestFocus();
            return;
        }
        if (password.isEmpty()) {
            binding.etPassword.setError("Password is required");
            binding.etPassword.requestFocus();
            return;
        }

        setLoading(true);
        hideError();

        Map<String, String> body = new HashMap<>();
        body.put("email",    email);
        body.put("password", password);

        ApiClient.getInstance(null)
            .login(body)
            .enqueue(new Callback<AuthResponse>() {
                @Override
                public void onResponse(Call<AuthResponse> call, Response<AuthResponse> response) {
                    setLoading(false);
                    if (response.isSuccessful() && response.body() != null) {
                        AuthResponse auth = response.body();

                        if (!"parent".equals(auth.user.role)) {
                            showError("This app is for parents only. Please use the web portal.");
                            return;
                        }

                        session.saveSession(
                            auth.token,
                            auth.user.id,
                            auth.user.name,
                            auth.user.email,
                            auth.user.role
                        );

                        Intent intent = new Intent(LoginActivity.this, DashboardActivity.class);
                        intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                        startActivity(intent);

                    } else if (response.code() == 422) {
                        showError("Invalid email or password. Please try again.");
                    } else if (response.code() == 403) {
                        showError("Your account is inactive. Contact the administrator.");
                    } else {
                        showError("Login failed. Please try again later.");
                    }
                }

                @Override
                public void onFailure(Call<AuthResponse> call, Throwable t) {
                    setLoading(false);
                    showError("Network error: " + t.getMessage());
                }
            });
    }

    private void setLoading(boolean loading) {
        binding.btnLogin.setEnabled(!loading);
        binding.progressBar.setVisibility(loading ? View.VISIBLE : View.GONE);
    }

    private void showError(String message) {
        binding.tvError.setText(message);
        binding.tvError.setVisibility(View.VISIBLE);
    }

    private void hideError() {
        binding.tvError.setVisibility(View.GONE);
    }
}
