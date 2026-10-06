import 'package:carousel_slider/carousel_slider.dart';
import 'package:flutter/material.dart';
import 'package:mybaap/core/color_constant.dart';
import 'package:mybaap/core/common_widgets/state_message.dart';
import 'package:mybaap/core/fonts_styles.dart';
import 'package:mybaap/modules/auth/providers/auth_provider.dart';
import 'package:mybaap/modules/home/models/catalog_models.dart';
import 'package:mybaap/modules/home/providers/home_provider.dart';
import 'package:mybaap/modules/home/widgets/video_card.dart';
import 'package:mybaap/routes/app_routes.dart';
import 'package:provider/provider.dart';

class HomeView extends StatefulWidget {
  const HomeView({super.key});

  @override
  State<HomeView> createState() => _HomeViewState();
}

class _HomeViewState extends State<HomeView> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<HomeProvider>().load();
      context.read<AuthProvider>().loadProfile();
    });
  }

  Future<void> _editName() async {
    final auth = context.read<AuthProvider>();
    final controller = TextEditingController(text: auth.user?.name ?? '');
    final name = await showDialog<String>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Your name'),
        content: TextField(controller: controller, decoration: const InputDecoration(hintText: 'Name')),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context), child: const Text('Cancel')),
          TextButton(onPressed: () => Navigator.pop(context, controller.text.trim()), child: const Text('Save')),
        ],
      ),
    );
    controller.dispose();
    if (name == null || name.isEmpty || !mounted) return;
    await auth.updateName(name);
  }

  void _openVideo(VideoItem video) {
    if (video.locked) {
      Navigator.pushNamed(context, AppRoutes.subscription);
      return;
    }
    Navigator.pushNamed(context, AppRoutes.player, arguments: video);
  }

  @override
  Widget build(BuildContext context) {
    final auth = context.watch<AuthProvider>();
    final home = context.watch<HomeProvider>();
    return Scaffold(
      appBar: AppBar(
        title: const Text('MyBaap', style: TextStyle(fontFamily: FontsStyle.bold, color: ColorConstant.primaryCl)),
        actions: [
          IconButton(
            onPressed: () async {
              await auth.logout();
              if (!context.mounted) return;
              Navigator.pushNamedAndRemoveUntil(context, AppRoutes.mobile, (route) => false);
            },
            icon: const Icon(Icons.logout),
          ),
        ],
      ),
      body: home.loading && home.videos.isEmpty
          ? const Center(child: CircularProgressIndicator(color: ColorConstant.primaryCl))
          : home.error != null && home.videos.isEmpty
              ? StateMessage(title: 'Could not load home', subtitle: home.error, onRetry: home.load)
              : RefreshIndicator(
                  color: ColorConstant.primaryCl,
                  onRefresh: () async {
                    await home.load();
                    await auth.loadProfile();
                  },
                  child: ListView(
                    children: [
                      Padding(
                        padding: const EdgeInsets.fromLTRB(16, 8, 16, 4),
                        child: Row(
                          children: [
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(auth.user?.displayName ?? 'Welcome', style: const TextStyle(fontFamily: FontsStyle.semiBold, fontSize: 18)),
                                  Text(auth.user?.mobile ?? '', style: const TextStyle(color: ColorConstant.bodyTextCl)),
                                  Text(
                                    auth.subscription.isActive
                                        ? 'Subscribed${auth.subscription.planName == null ? '' : ' · ${auth.subscription.planName}'}'
                                        : 'No active subscription',
                                    style: const TextStyle(color: ColorConstant.primaryDarkCl, fontSize: 12),
                                  ),
                                ],
                              ),
                            ),
                            IconButton(onPressed: _editName, icon: const Icon(Icons.edit_outlined)),
                          ],
                        ),
                      ),
                      if (home.banners.isNotEmpty)
                        CarouselSlider(
                          items: home.banners.map(_banner).toList(),
                          options: CarouselOptions(height: 160, autoPlay: home.banners.length > 1, viewportFraction: 0.9),
                        ),
                      const SizedBox(height: 12),
                      SizedBox(
                        height: 42,
                        child: ListView(
                          scrollDirection: Axis.horizontal,
                          padding: const EdgeInsets.symmetric(horizontal: 16),
                          children: [
                            _chip('All', home.selectedCategoryId == null, () => home.selectCategory(null)),
                            ...home.categories.map(
                              (category) => _chip(
                                category.name,
                                home.selectedCategoryId == category.id,
                                () => home.selectCategory(category.id),
                              ),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 8),
                      if (home.videos.isEmpty)
                        const Padding(
                          padding: EdgeInsets.all(32),
                          child: StateMessage(title: 'No videos yet'),
                        )
                      else
                        ...home.videos.map((video) => VideoCard(video: video, onTap: () => _openVideo(video))),
                      const SizedBox(height: 24),
                    ],
                  ),
                ),
    );
  }

  Widget _banner(BannerItem banner) {
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 6),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(16),
        color: ColorConstant.primaryCl,
      ),
      clipBehavior: Clip.antiAlias,
      child: Stack(
        fit: StackFit.expand,
        children: [
          if (banner.imageUrl != null)
            Image.network(banner.imageUrl!, fit: BoxFit.cover, errorBuilder: (_, _, _) => const SizedBox.shrink()),
          Container(
            alignment: Alignment.bottomLeft,
            padding: const EdgeInsets.all(16),
            child: Text(banner.title, style: const TextStyle(color: ColorConstant.white, fontFamily: FontsStyle.semiBold, fontSize: 16)),
          ),
        ],
      ),
    );
  }

  Widget _chip(String label, bool selected, VoidCallback onTap) {
    return Padding(
      padding: const EdgeInsets.only(right: 8),
      child: ChoiceChip(
        label: Text(label),
        selected: selected,
        selectedColor: ColorConstant.primaryCl,
        labelStyle: TextStyle(
          color: selected ? ColorConstant.white : ColorConstant.primaryDarkCl,
          fontFamily: FontsStyle.medium,
        ),
        backgroundColor: ColorConstant.primaryLightCl,
        onSelected: (_) => onTap(),
      ),
    );
  }
}
