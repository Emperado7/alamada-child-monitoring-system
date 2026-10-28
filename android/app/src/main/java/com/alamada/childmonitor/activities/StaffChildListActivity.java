package com.alamada.childmonitor.activities;

import android.content.Intent;
import android.os.Bundle;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.View;
import android.widget.Toast;

import androidx.recyclerview.widget.LinearLayoutManager;

import com.alamada.childmonitor.R;
import com.alamada.childmonitor.adapters.ChildCardAdapter;
import com.alamada.childmonitor.databinding.ActivityStaffChildListBinding;
import com.alamada.childmonitor.models.Child;
import com.alamada.childmonitor.models.ChildListResponse;
import com.alamada.childmonitor.network.ApiClient;

import java.util.ArrayList;
import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

/**
 * Staff / Admin child list screen — shows all enrolled children with search.
 * Tapping a child opens ChildDetailActivity (shared with parent view).
 */
public class StaffChildListActivity extends BaseActivity {

    private ActivityStaffChildListBinding binding;
    private ChildCardAdapter adapter;
    private final List<Child> allChildren = new ArrayList<>();
    private final List<Child> filteredChildren = new ArrayList<>();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivityStaffChildListBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        if (getSupportActionBar() != null) getSupportActionBar().hide();

        binding.btnBack.setOnClickListener(v -> finish());

        adapter = new ChildCardAdapter(filteredChildren, child -> {
            Intent i = new Intent(this, ChildDetailActivity.class);
            i.putExtra("child_id", child.id);
            startActivity(i);
        });
        binding.rvChildren.setLayoutManager(new LinearLayoutManager(this));
        binding.rvChildren.setAdapter(adapter);

        binding.swipeRefresh.setColorSchemeResources(R.color.primary);
        binding.swipeRefresh.setOnRefreshListener(this::loadChildren);

        // Search filter
        binding.etSearch.addTextChangedListener(new TextWatcher() {
            @Override public void beforeTextChanged(CharSequence s, int start, int count, int after) {}
            @Override public void onTextChanged(CharSequence s, int start, int before, int count) {
                filterChildren(s.toString());
            }
            @Override public void afterTextChanged(Editable s) {}
        });

        // ── Bottom nav ─────────────────────────────────────────────
        binding.tabHome.setOnClickListener(v -> {
            startActivity(new Intent(this, StaffDashboardActivity.class));
            finish();
        });
        binding.tabNotifications.setOnClickListener(v ->
            startActivity(new Intent(this, NotificationsActivity.class)));
        binding.tabSettings.setOnClickListener(v ->
            startActivity(new Intent(this, SettingsActivity.class)));

        loadChildren();
    }

    private void loadChildren() {
        binding.progressBar.setVisibility(View.VISIBLE);
        ApiClient.getInstance(session.getBearerToken())
            .getAllChildren()
            .enqueue(new Callback<ChildListResponse>() {
                @Override
                public void onResponse(Call<ChildListResponse> call,
                                       Response<ChildListResponse> response) {
                    binding.progressBar.setVisibility(View.GONE);
                    binding.swipeRefresh.setRefreshing(false);
                    if (response.code() == 401) { handleUnauthorised(); return; }
                    if (response.isSuccessful() && response.body() != null
                            && response.body().data != null) {
                        allChildren.clear();
                        allChildren.addAll(response.body().data);
                        filterChildren(binding.etSearch.getText().toString());
                    }
                }
                @Override
                public void onFailure(Call<ChildListResponse> call, Throwable t) {
                    binding.progressBar.setVisibility(View.GONE);
                    binding.swipeRefresh.setRefreshing(false);
                    Toast.makeText(StaffChildListActivity.this,
                        "Could not load children.", Toast.LENGTH_SHORT).show();
                }
            });
    }

    private void filterChildren(String query) {
        filteredChildren.clear();
        if (query == null || query.trim().isEmpty()) {
            filteredChildren.addAll(allChildren);
        } else {
            String q = query.toLowerCase();
            for (Child c : allChildren) {
                String name = c.getDisplayName().toLowerCase();
                if (name.contains(q)) filteredChildren.add(c);
            }
        }
        adapter.notifyDataSetChanged();
        boolean empty = filteredChildren.isEmpty();
        binding.tvEmpty.setVisibility(empty ? View.VISIBLE : View.GONE);
        binding.rvChildren.setVisibility(empty ? View.GONE : View.VISIBLE);
    }
}
