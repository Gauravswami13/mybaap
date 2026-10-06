import 'package:flutter/foundation.dart';
import 'package:mybaap/services/api_client.dart';

class FaqItem {
  const FaqItem({required this.id, required this.question, required this.answer});

  final int id;
  final String question;
  final String answer;

  factory FaqItem.fromJson(Map<String, dynamic> json) {
    return FaqItem(
      id: json['id'] as int,
      question: json['question'] as String? ?? '',
      answer: json['answer'] as String? ?? '',
    );
  }
}

class ContentProvider extends ChangeNotifier {
  ContentProvider(this.api);

  final ApiClient api;

  bool loading = false;
  String? error;
  String pageTitle = '';
  String pageContent = '';
  List<FaqItem> faqs = [];

  Future<void> loadPage(String slug) async {
    loading = true;
    error = null;
    pageTitle = '';
    pageContent = '';
    notifyListeners();
    try {
      final data = await api.get('/pages/$slug');
      if (data is Map) {
        pageTitle = data['title'] as String? ?? '';
        pageContent = data['content'] as String? ?? '';
      }
      loading = false;
      notifyListeners();
    } on ApiException catch (exception) {
      loading = false;
      error = exception.message;
      notifyListeners();
    } catch (_) {
      loading = false;
      error = 'Unable to load this page.';
      notifyListeners();
    }
  }

  Future<void> loadFaqs() async {
    loading = true;
    error = null;
    notifyListeners();
    try {
      final data = await api.get('/faqs');
      faqs = data is List
          ? data.whereType<Map>().map((item) => FaqItem.fromJson(Map<String, dynamic>.from(item))).toList()
          : [];
      loading = false;
      notifyListeners();
    } on ApiException catch (exception) {
      loading = false;
      error = exception.message;
      notifyListeners();
    } catch (_) {
      loading = false;
      error = 'Unable to load FAQs.';
      notifyListeners();
    }
  }
}
