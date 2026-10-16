import 'package:flutter/material.dart';
import 'package:mybaap/core/color_constant.dart';
import 'package:mybaap/core/common_widgets/app_image.dart';
import 'package:mybaap/core/common_widgets/custom_button.dart';
import 'package:mybaap/core/fonts_styles.dart';
import 'package:mybaap/modules/auth/providers/auth_provider.dart';
import 'package:mybaap/modules/home/models/catalog_models.dart';
import 'package:mybaap/modules/home/providers/home_provider.dart';
import 'package:mybaap/modules/home/widgets/video_card.dart';
import 'package:mybaap/routes/app_routes.dart';
import 'package:provider/provider.dart';

class VideoDetailView extends StatelessWidget {
  const VideoDetailView({super.key, required this.video});

  final VideoItem video;

  Future<void> _watch(BuildContext context) async {
    final subscribed = context.read<AuthProvider>().subscription.isActive;
    if (video.locked && !subscribed) {
      await Navigator.pushNamed(context, AppRoutes.subscription);
      return;
    }
    if (!context.mounted) return;
    await Navigator.pushNamed(context, AppRoutes.player, arguments: video);
    if (!context.mounted) return;
    await context.read<HomeProvider>().load();
  }

  @override
  Widget build(BuildContext context) {
    final home = context.watch<HomeProvider>();
    final subscribed = context.watch<AuthProvider>().subscription.isActive;
    final locked = video.locked && !subscribed;
    final related = home.videos.where((item) => item.id != video.id && item.categoryName == video.categoryName).toList();
    final meta = [
      if (video.durationLabel.isNotEmpty) video.durationLabel,
      video.isPaid ? 'Paid' : 'Free',
    ].join('  ·  ');

    return Scaffold(
      backgroundColor: ColorConstant.bgCl,
      body: ListView(
        padding: EdgeInsets.zero,
        children: [
          Stack(
            children: [
              AspectRatio(
                aspectRatio: 16 / 9,
                child: AppImage(url: video.thumbnailUrl, icon: Icons.movie_outlined),
              ),
              const Positioned.fill(
                child: DecoratedBox(
                  decoration: BoxDecoration(
                    gradient: LinearGradient(
                      begin: Alignment.topCenter,
                      end: Alignment.bottomCenter,
                      colors: [Color(0x88010A0F), Color(0x00010A0F), Color(0xF2010A0F)],
                    ),
                  ),
                ),
              ),
              SafeArea(
                child: IconButton(
                  onPressed: () => Navigator.maybePop(context),
                  icon: const Icon(Icons.arrow_back_ios_new_rounded, color: ColorConstant.white),
                ),
              ),
            ],
          ),
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 8, 16, 24),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                if (video.categoryName != null && video.categoryName!.isNotEmpty)
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                    decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(20),
                      border: Border.all(color: ColorConstant.lightBorderCl),
                    ),
                    child: Text(video.categoryName!, style: const TextStyle(color: ColorConstant.white, fontSize: 12)),
                  ),
                const SizedBox(height: 12),
                Text(video.title, style: const TextStyle(color: ColorConstant.white, fontFamily: FontsStyle.bold, fontSize: 28, height: 1.15)),
                const SizedBox(height: 10),
                Row(
                  children: [
                    const Icon(Icons.schedule_rounded, color: ColorConstant.lightGrayIcCl, size: 16),
                    const SizedBox(width: 6),
                    Text(meta, style: const TextStyle(color: ColorConstant.lightGrayIcCl, fontSize: 13)),
                  ],
                ),
                if (video.description != null && video.description!.isNotEmpty) ...[
                  const SizedBox(height: 14),
                  Text(video.description!, style: const TextStyle(color: ColorConstant.lightGrayIcCl, height: 1.45, fontFamily: FontsStyle.regular, fontSize: 14)),
                ],
                const SizedBox(height: 18),
                SizedBox(
                  width: locked ? 210 : 170,
                  child: CustomButton(
                    text: locked ? 'Get Subscription' : 'Watch now',
                    icon: locked ? Icons.lock_outline_rounded : Icons.play_arrow_rounded,
                    height: 46,
                    onPressed: () => _watch(context),
                  ),
                ),
                if (locked) ...[
                  const SizedBox(height: 8),
                  const Text('This video plays after you take a plan.', style: TextStyle(color: ColorConstant.bodyTextCl, fontSize: 12)),
                ],
                if (related.isNotEmpty) ...[
                  const SizedBox(height: 28),
                  const Text('More like this', style: TextStyle(color: ColorConstant.white, fontFamily: FontsStyle.bold, fontSize: 18)),
                  const SizedBox(height: 12),
                  SizedBox(
                    height: 230,
                    child: ListView.separated(
                      scrollDirection: Axis.horizontal,
                      itemCount: related.length,
                      separatorBuilder: (_, _) => const SizedBox(width: 10),
                      itemBuilder: (context, index) {
                        final item = related[index];
                        return VideoPoster(
                          video: item,
                          onTap: () => Navigator.pushReplacementNamed(context, AppRoutes.videoDetail, arguments: item),
                        );
                      },
                    ),
                  ),
                ],
              ],
            ),
          ),
        ],
      ),
    );
  }
}
