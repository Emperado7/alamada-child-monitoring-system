package com.alamada.childmonitor.fragments;

import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.fragment.app.Fragment;

import com.alamada.childmonitor.R;
import com.alamada.childmonitor.models.ChildResponse;

public class InfoFragment extends Fragment {

    private static final String KEY_FULL_NAME = "full_name";
    private static final String KEY_DOB       = "dob";
    private static final String KEY_GENDER    = "gender";
    private static final String KEY_DIAGNOSIS = "diagnosis";
    private static final String KEY_ADDRESS   = "address";
    private static final String KEY_GUARDIAN  = "guardian";
    private static final String KEY_CONTACT   = "contact";

    public static InfoFragment newInstance(ChildResponse child) {
        InfoFragment f = new InfoFragment();
        Bundle args = new Bundle();

        String name = (child.fullName != null && !child.fullName.isEmpty())
            ? child.fullName
            : (child.firstName + " " + child.lastName);

        args.putString(KEY_FULL_NAME, name);
        args.putString(KEY_DOB,       safe(child.dateOfBirth));
        args.putString(KEY_GENDER,    capitalize(child.gender));
        args.putString(KEY_DIAGNOSIS, safe(child.diagnosis));
        args.putString(KEY_ADDRESS,   safe(child.address));
        args.putString(KEY_GUARDIAN,  safe(child.guardianName));
        args.putString(KEY_CONTACT,   safe(child.guardianContact));
        f.setArguments(args);
        return f;
    }

    @Nullable
    @Override
    public View onCreateView(@NonNull LayoutInflater inflater,
                             @Nullable ViewGroup container,
                             @Nullable Bundle savedInstanceState) {
        return inflater.inflate(R.layout.fragment_tab_info, container, false);
    }

    @Override
    public void onViewCreated(@NonNull View view, @Nullable Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);

        Bundle a = getArguments();
        if (a == null) return;

        setText(view, R.id.tv_full_name,      a.getString(KEY_FULL_NAME, "—"));
        setText(view, R.id.tv_dob,            a.getString(KEY_DOB,       "—"));
        setText(view, R.id.tv_gender_info,    a.getString(KEY_GENDER,    "—"));
        setText(view, R.id.tv_diagnosis_info, a.getString(KEY_DIAGNOSIS, "—"));
        setText(view, R.id.tv_address,        a.getString(KEY_ADDRESS,   "—"));
        setText(view, R.id.tv_guardian_info,  a.getString(KEY_GUARDIAN,  "—"));
        setText(view, R.id.tv_contact_info,   a.getString(KEY_CONTACT,   "—"));
    }

    private void setText(View root, int id, String value) {
        TextView tv = root.findViewById(id);
        if (tv != null) tv.setText(value);
    }

    private static String safe(String s) {
        return (s != null && !s.isEmpty()) ? s : "—";
    }

    private static String capitalize(String s) {
        if (s == null || s.isEmpty()) return "—";
        return Character.toUpperCase(s.charAt(0)) + s.substring(1);
    }
}
