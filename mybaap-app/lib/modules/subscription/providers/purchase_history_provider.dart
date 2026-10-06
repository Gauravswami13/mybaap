import 'package:flutter/foundation.dart';
import 'package:mybaap/services/api_client.dart';

class PurchaseItem {
  const PurchaseItem({
    required this.id,
    required this.planName,
    required this.price,
    required this.startDate,
    required this.endDate,
    required this.status,
    required this.paymentRef,
    required this.createdAt,
  });

  final int id;
  final String planName;
  final String price;
  final String startDate;
  final String endDate;
  final String status;
  final String? paymentRef;
  final String createdAt;

  factory PurchaseItem.fromJson(Map<String, dynamic> json) {
    return PurchaseItem(
      id: json['id'] as int? ?? 0,
      planName: json['plan_name'] as String? ?? 'Plan',
      price: json['price']?.toString() ?? '',
      startDate: json['start_date'] as String? ?? '',
      endDate: json['end_date'] as String? ?? '',
      status: json['status'] as String? ?? '',
      paymentRef: json['payment_ref'] as String?,
      createdAt: json['created_at'] as String? ?? '',
    );
  }
}

class PurchaseHistoryProvider extends ChangeNotifier {
  PurchaseHistoryProvider(this.api);

  final ApiClient api;
  final List<PurchaseItem> items = [];
  bool loading = false;
  bool loadingMore = false;
  String? error;
  int page = 1;
  int lastPage = 1;

  bool get hasMore => page < lastPage;

  Future<void> load() async {
    loading = true;
    error = null;
    page = 1;
    items.clear();
    notifyListeners();
    try {
      final data = await api.get('/subscription/history', query: {'per_page': 15, 'page': 1});
      _apply(data, replace: true);
    } on ApiException catch (exception) {
      error = exception.message;
    } finally {
      loading = false;
      notifyListeners();
    }
  }

  Future<void> loadMore() async {
    if (!hasMore || loadingMore || loading) return;
    loadingMore = true;
    notifyListeners();
    try {
      final data = await api.get('/subscription/history', query: {'per_page': 15, 'page': page + 1});
      _apply(data, replace: false);
    } on ApiException catch (exception) {
      error = exception.message;
    } finally {
      loadingMore = false;
      notifyListeners();
    }
  }

  void _apply(dynamic data, {required bool replace}) {
    final map = data as Map<String, dynamic>;
    final raw = map['items'] as List<dynamic>? ?? [];
    final parsed = raw.map((item) => PurchaseItem.fromJson(item as Map<String, dynamic>)).toList();
    if (replace) {
      items
        ..clear()
        ..addAll(parsed);
    } else {
      items.addAll(parsed);
    }
    final pagination = map['pagination'] as Map<String, dynamic>? ?? {};
    page = pagination['current_page'] as int? ?? page;
    lastPage = pagination['last_page'] as int? ?? page;
  }
}
