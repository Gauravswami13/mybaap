import 'package:flutter/foundation.dart';
import 'package:mybaap/modules/subscription/models/plan_model.dart';
import 'package:mybaap/services/api_client.dart';

class SubscriptionProvider extends ChangeNotifier {
  SubscriptionProvider(this.api);

  final ApiClient api;
  bool loading = false;
  String? error;
  List<PlanModel> plans = [];
  bool isActive = false;
  String? planName;
  String? endDate;
  bool paying = false;

  Future<void> load() async {
    loading = true;
    error = null;
    notifyListeners();
    try {
      final planData = await api.get('/plans');
      final status = await api.get('/subscription');
      plans = planData is List
          ? planData.whereType<Map>().map((item) => PlanModel.fromJson(Map<String, dynamic>.from(item))).toList()
          : [];
      if (status is Map) {
        isActive = status['is_active'] == true;
        final subscription = status['subscription'];
        if (subscription is Map) {
          planName = subscription['plan_name'] as String?;
          endDate = subscription['end_date'] as String?;
        } else {
          planName = null;
          endDate = null;
        }
      }
      loading = false;
      notifyListeners();
    } on ApiException catch (exception) {
      loading = false;
      error = exception.message;
      notifyListeners();
    } catch (_) {
      loading = false;
      error = 'Unable to load plans.';
      notifyListeners();
    }
  }

  Future<Map<String, dynamic>> createOrder(int planId) async {
    paying = true;
    notifyListeners();
    try {
      final data = await api.post('/payments/razorpay/order', body: {'plan_id': planId});
      paying = false;
      notifyListeners();
      if (data is Map) return Map<String, dynamic>.from(data);
      throw ApiException('Could not start payment.');
    } catch (exception) {
      paying = false;
      notifyListeners();
      if (exception is ApiException) rethrow;
      throw ApiException('Could not start payment.');
    }
  }

  Future<void> verifyPayment({
    required int planId,
    required String orderId,
    required String paymentId,
    required String signature,
  }) async {
    paying = true;
    notifyListeners();
    try {
      await api.post('/payments/razorpay/verify', body: {
        'plan_id': planId,
        'razorpay_order_id': orderId,
        'razorpay_payment_id': paymentId,
        'razorpay_signature': signature,
      });
      paying = false;
      await load();
    } catch (exception) {
      paying = false;
      notifyListeners();
      if (exception is ApiException) rethrow;
      throw ApiException('Payment could not be verified.');
    }
  }
}
