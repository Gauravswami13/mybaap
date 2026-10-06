import 'package:flutter/foundation.dart';
import 'package:mybaap/services/api_client.dart';
import 'package:share_plus/share_plus.dart';

class ShareProvider extends ChangeNotifier {
  ShareProvider(this.api);

  final ApiClient api;
  static const fallbackText = 'Watch videos on MyBaap. https://play.google.com/store/apps/details?id=com.mybaap.mybaap';

  Future<void> shareApp() async {
    var text = fallbackText;
    try {
      final data = await api.get('/settings/public');
      final map = data as Map<String, dynamic>;
      final message = (map['app_share_text'] as String?)?.trim();
      final url = (map['play_store_url'] as String?)?.trim() ?? '';
      if (message != null && message.isNotEmpty) {
        text = url.isEmpty ? message : '$message $url';
      }
    } on ApiException {
      text = fallbackText;
    }
    await SharePlus.instance.share(ShareParams(text: text));
  }
}
