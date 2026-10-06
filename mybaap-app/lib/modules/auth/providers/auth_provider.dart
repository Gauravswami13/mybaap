import 'package:flutter/foundation.dart';
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
    if (data is Map && data['user'] is Map) {
      user = UserModel.fromJson(Map<String, dynamic>.from(data['user'] as Map));
      subscription = SubscriptionStatus.fromProfile(data);
      notifyListeners();
    }
  }

  Future<void> updateName(String name) async {
    final data = await _run(() => api.put('/profile', body: {'name': name}));
    if (data is Map && data['user'] is Map) {
      user = UserModel.fromJson(Map<String, dynamic>.from(data['user'] as Map));
      subscription = SubscriptionStatus.fromProfile(data);
      notifyListeners();
    }
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
