import 'package:flutter/foundation.dart';
import 'package:mybaap/modules/home/models/catalog_models.dart';
import 'package:mybaap/services/api_client.dart';

class HomeProvider extends ChangeNotifier {
  HomeProvider(this.api);

  final ApiClient api;

  bool loading = false;
  String? error;
  List<BannerItem> banners = [];
  List<CategoryItem> categories = [];
  List<VideoItem> videos = [];
  int? selectedCategoryId;

  Future<void> load() async {
    loading = true;
    error = null;
    notifyListeners();
    try {
      if (selectedCategoryId == null) {
        final data = await api.get('/home');
        if (data is Map) {
          banners = _list(data['banners']).map(BannerItem.fromJson).toList();
          categories = _list(data['categories']).map(CategoryItem.fromJson).toList();
          videos = _list(data['videos']).map(VideoItem.fromJson).toList();
        }
      } else {
        final data = await api.get('/videos', query: {'category_id': selectedCategoryId});
        if (data is Map) {
          videos = _list(data['videos']).map(VideoItem.fromJson).toList();
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
      error = 'Unable to load videos.';
      notifyListeners();
    }
  }

  Future<void> selectCategory(int? id) async {
    selectedCategoryId = id;
    await load();
  }

  Future<String> playbackUrl(int id) async {
    final data = await api.get('/videos/$id/playback');
    if (data is Map && data['url'] is String) {
      return data['url'] as String;
    }
    throw ApiException('Playback URL was not returned.');
  }

  List<Map<String, dynamic>> _list(dynamic value) {
    if (value is! List) return [];
    return value.whereType<Map>().map((item) => Map<String, dynamic>.from(item)).toList();
  }
}
