import 'dart:async';

import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:mybaap/modules/auth/models/user_model.dart';
import 'package:mybaap/services/api_client.dart';
import 'package:mybaap/services/token_store.dart';

class AuthProvider extends ChangeNotifier {
  AuthProvider(this.api, this.tokenStore);

  final ApiClient api;
  final TokenStore tokenStore;

  UserModel? user;
  SubscriptionStatus subscription = const SubscriptionStatus(isActive: false);
  bool loading = false;
  String? error;
  String pendingMobile = '';
  int otpSeconds = 0;
  final mobileController = TextEditingController();
  final otpController = TextEditingController();
  String? pendingPhotoPath;
  Timer? _otpTimer;

  Future<bool> hasSession() async {
    final token = await tokenStore.read();
    if (token == null || token.isEmpty) return false;
    try {
      await loadProfile();
      return true;
    } catch (_) {
      await tokenStore.clear();
      return false;
    }
  }

  Future<bool> continueWithMobile() async {
    final mobile = mobileController.text.trim();
    if (!RegExp(r'^[6-9][0-9]{9}$').hasMatch(mobile)) {
      error = 'Enter a valid 10 digit mobile number.';
      notifyListeners();
      return false;
    }
    pendingMobile = mobile;
    try {
      await sendOtp(mobile);
      otpController.clear();
      _startOtpTimer();
      return true;
    } catch (_) {
      return false;
    }
  }

  Future<bool> submitOtp() async {
    final code = otpController.text.trim();
    if (code.length != 6) {
      error = 'Enter the 6 digit OTP.';
      notifyListeners();
      return false;
    }
    try {
      await verifyOtp(pendingMobile, code);
      _otpTimer?.cancel();
      otpSeconds = 0;
      otpController.clear();
      notifyListeners();
      return true;
    } catch (_) {
      return false;
    }
  }

  Future<void> resendOtp() async {
    if (otpSeconds > 0 || loading || pendingMobile.isEmpty) return;
    try {
      await sendOtp(pendingMobile);
      _startOtpTimer();
    } catch (_) {}
  }

  void _startOtpTimer() {
    _otpTimer?.cancel();
    otpSeconds = 30;
    notifyListeners();
    _otpTimer = Timer.periodic(const Duration(seconds: 1), (timer) {
      if (otpSeconds <= 1) {
        otpSeconds = 0;
        timer.cancel();
      } else {
        otpSeconds -= 1;
      }
      notifyListeners();
    });
  }

  Future<void> sendOtp(String mobile) async {
    await _run(() => api.post('/auth/send-otp', body: {'mobile': mobile}));
  }

  Future<void> verifyOtp(String mobile, String otp) async {
    final data = await _run(() => api.post('/auth/verify-otp', body: {'mobile': mobile, 'otp': otp}));
    if (data is Map && data['token'] is String && data['user'] is Map) {
      await tokenStore.write(data['token'] as String);
      user = UserModel.fromJson(Map<String, dynamic>.from(data['user'] as Map));
      notifyListeners();
    }
  }

  Future<void> loadProfile() async {
    final data = await _run(() => api.get('/profile'));
    _applyProfile(data);
  }

  void _applyProfile(dynamic data) {
    if (data is Map && data['user'] is Map) {
      user = UserModel.fromJson(Map<String, dynamic>.from(data['user'] as Map));
      subscription = SubscriptionStatus.fromProfile(data);
      notifyListeners();
    }
  }

  Future<void> updateName(String name) async {
    await saveProfile(name);
  }

  Future<void> pickPhoto() async {
    final file = await ImagePicker().pickImage(source: ImageSource.gallery, imageQuality: 80, maxWidth: 1200);
    if (file == null) return;
    pendingPhotoPath = file.path;
    notifyListeners();
  }

  Future<void> saveProfile(String name) async {
    final trimmed = name.trim();
    if (trimmed.isEmpty) {
      error = 'Name is required';
      notifyListeners();
      throw ApiException('Name is required');
    }
    final data = pendingPhotoPath == null
        ? await _run(() => api.put('/profile', body: {'name': trimmed}))
        : await _run(() => api.upload('/profile', fields: {'name': trimmed}, filePath: pendingPhotoPath));
    _applyProfile(data);
    pendingPhotoPath = null;
    notifyListeners();
  }

  Future<void> deleteAccount() async {
    await _run(() => api.delete('/account', body: {'confirm': true}));
    await tokenStore.clear();
    user = null;
    subscription = const SubscriptionStatus(isActive: false);
    notifyListeners();
  }

  Future<void> logout() async {
    try {
      await api.post('/auth/logout');
    } catch (_) {}
    await tokenStore.clear();
    user = null;
    subscription = const SubscriptionStatus(isActive: false);
    notifyListeners();
  }

  @override
  void dispose() {
    _otpTimer?.cancel();
    mobileController.dispose();
    otpController.dispose();
    super.dispose();
  }

  Future<dynamic> _run(Future<dynamic> Function() call) async {
    loading = true;
    error = null;
    notifyListeners();
    try {
      final result = await call();
      loading = false;
      notifyListeners();
      return result;
    } on ApiException catch (exception) {
      loading = false;
      error = exception.message;
      notifyListeners();
      rethrow;
    } catch (_) {
      loading = false;
      error = 'Something went wrong.';
      notifyListeners();
      rethrow;
    }
  }
}
