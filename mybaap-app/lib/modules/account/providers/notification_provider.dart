import 'package:flutter/foundation.dart';
import 'package:mybaap/services/api_client.dart';

class AppNotice {
  const AppNotice({required this.id, required this.title, required this.body, required this.createdAt});

  final int id;
  final String title;
  final String body;
  final String createdAt;

  factory AppNotice.fromJson(Map<String, dynamic> json) {
    return AppNotice(
      id: json['id'] as int? ?? 0,
      title: json['title'] as String? ?? '',
      body: json['body'] as String? ?? '',
      createdAt: json['created_at'] as String? ?? '',
    );
  }
}

class NotificationProvider extends ChangeNotifier {
  NotificationProvider(this.api);

  final ApiClient api;
  List<AppNotice> items = [];
  bool loading = false;
  String? error;

  Future<void> load() async {
    loading = true;
    error = null;
    notifyListeners();
    try {
      final data = await api.get('/notifications');
      items = data is List
          ? data.map((item) => AppNotice.fromJson(Map<String, dynamic>.from(item as Map))).toList()
          : [];
    } on ApiException catch (exception) {
      error = exception.message;
    } finally {
      loading = false;
      notifyListeners();
    }
  }
}
