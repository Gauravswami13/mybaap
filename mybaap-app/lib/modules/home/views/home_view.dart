import 'package:carousel_slider/carousel_slider.dart';
import 'package:flutter/material.dart';
import 'package:mybaap/core/color_constant.dart';
import 'package:mybaap/core/common_widgets/app_image.dart';
import 'package:mybaap/core/common_widgets/custom_button.dart';
import 'package:mybaap/core/fonts_styles.dart';
import 'package:mybaap/modules/account/views/account_view.dart';
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
  int _tab = 0;
  String? _category;
  final _search = TextEditingController();

  @override
  void dispose() {
    _search.dispose();
    super.dispose();
  }

  Future<void> _open(VideoItem video) async {
    await Navigator.pushNamed(context, AppRoutes.videoDetail, arguments: video);
    if (!mounted) return;
    await context.read<HomeProvider>().load();
  }

  @override
  Widget build(BuildContext context) {
    final auth = context.watch<AuthProvider>();
    final home = context.watch<HomeProvider>();
    if (!home.bootstrapped) {
      WidgetsBinding.instance.addPostFrameCallback((_) {
        home.bootstrap(auth.loadProfile);
      });
    }
    if (_tab == 1 && !home.catalogReady) {
      WidgetsBinding.instance.addPostFrameCallback((_) => home.loadCatalog());
    }
    final query = _search.text.trim().toLowerCase();
    final source = home.catalogReady ? home.catalog : home.videos;
    final results = source.where((video) {
      if (_category != null && video.categoryName != _category) return false;
      if (query.isEmpty) return true;
      return video.title.toLowerCase().contains(query) || (video.categoryName ?? '').toLowerCase().contains(query);
    }).toList();

    return Scaffold(
      backgroundColor: ColorConstant.bgCl,
      body: IndexedStack(
        index: _tab,
        children: [
          _Browse(
            home: home,
            onPlay: _open,
            onSubscribe: () => setState(() => _tab = 2),
            onSearch: () => setState(() => _tab = 1),
          ),
          _Search(
            controller: _search,
            categories: home.categories,
            selected: _category,
            results: results,
            onChanged: () => setState(() {}),
            onCategory: (name) => setState(() => _category = name),
            onPlay: _open,
          ),
          const AccountView(embedded: true),
        ],
      ),
      bottomNavigationBar: NavigationBar(
        backgroundColor: const Color(0xFF071014),
        indicatorColor: ColorConstant.primaryCl.withValues(alpha: 0.2),
        selectedIndex: _tab,
        labelBehavior: NavigationDestinationLabelBehavior.alwaysShow,
        onDestinationSelected: (index) => setState(() => _tab = index),
        destinations: const [
          NavigationDestination(icon: Icon(Icons.home_outlined), selectedIcon: Icon(Icons.home), label: 'Home'),
          NavigationDestination(icon: Icon(Icons.search), label: 'Search'),
          NavigationDestination(icon: Icon(Icons.person_outline), selectedIcon: Icon(Icons.person), label: 'Profile'),
        ],
      ),
    );
  }
}

class _Browse extends StatelessWidget {
  const _Browse({required this.home, required this.onPlay, required this.onSubscribe, required this.onSearch});

  final HomeProvider home;
  final ValueChanged<VideoItem> onPlay;
  final VoidCallback onSubscribe;
  final VoidCallback onSearch;

  @override
  Widget build(BuildContext context) {
    final showLoader = !home.bootstrapped || (home.loading && home.categories.isEmpty && home.banners.isEmpty);
    if (showLoader) {
      return const Center(child: CircularProgressIndicator(color: ColorConstant.primaryCl));
    }
    if (home.error != null && home.categories.isEmpty) {
      return Center(
        child: Text(home.error!, style: const TextStyle(color: ColorConstant.lightGrayIcCl)),
      );
    }
    final featured = home.videos.isNotEmpty ? home.videos.first : null;
    final subscribed = context.watch<AuthProvider>().subscription.isActive;
    final needsPlan = featured != null && featured.locked && !subscribed;
    final paid = home.videos.where((video) => video.isPaid).toList();
    final free = home.videos.where((video) => !video.isPaid).toList();
    final bottomBanner = home.banners.isNotEmpty ? home.banners.last : null;
    return RefreshIndicator(
      color: ColorConstant.primaryCl,
      onRefresh: () async {
        await home.load();
        if (!context.mounted) return;
        await context.read<AuthProvider>().loadProfile();
      },
      child: ListView(
        padding: const EdgeInsets.only(bottom: 24),
        children: [
          SafeArea(
            bottom: false,
            child: Padding(
              padding: const EdgeInsets.fromLTRB(16, 8, 8, 8),
              child: Row(
                children: [
                  const CircleAvatar(
                    radius: 14,
                    backgroundColor: ColorConstant.primaryCl,
                    child: Icon(Icons.play_arrow_rounded, color: ColorConstant.white, size: 18),
                  ),
                  const SizedBox(width: 8),
                  const Text(
                    'MYBAAP',
                    style: TextStyle(
                      color: ColorConstant.primaryCl,
                      fontFamily: FontsStyle.bold,
                      fontSize: 20,
                      letterSpacing: 0.6,
                    ),
                  ),
                  const Spacer(),
                  IconButton(
                    onPressed: onSearch,
                    icon: const Icon(Icons.search, color: ColorConstant.white),
                  ),
                ],
              ),
            ),
          ),
          if (home.banners.isNotEmpty)
            CarouselSlider(
              items: home.banners
                  .map(
                    (banner) => _Hero(
                      banner: banner,
                      video: featured,
                      needsPlan: needsPlan,
                      onPlay: onPlay,
                      onSubscribe: onSubscribe,
                    ),
                  )
                  .toList(),
              options: CarouselOptions(height: 360, viewportFraction: 1, autoPlay: home.banners.length > 1),
            )
          else if (featured != null)
            _Hero(
              banner: BannerItem(id: 0, title: featured.title),
              video: featured,
              needsPlan: needsPlan,
              onPlay: onPlay,
              onSubscribe: onSubscribe,
            ),
          if (home.categories.isNotEmpty)
            SizedBox(
              height: 44,
              child: ListView.separated(
                padding: const EdgeInsets.fromLTRB(16, 8, 16, 0),
                scrollDirection: Axis.horizontal,
                itemCount: home.categories.length,
                separatorBuilder: (_, _) => const SizedBox(width: 8),
                itemBuilder: (context, index) {
                  final category = home.categories[index];
                  return InkWell(
                    onTap: () => Navigator.pushNamed(context, AppRoutes.categoryVideos, arguments: category),
                    borderRadius: BorderRadius.circular(20),
                    child: Container(
                      alignment: Alignment.center,
                      padding: const EdgeInsets.symmetric(horizontal: 14),
                      decoration: BoxDecoration(
                        borderRadius: BorderRadius.circular(20),
                        border: Border.all(color: ColorConstant.lightBorderCl),
                      ),
                      child: Text(category.name, style: const TextStyle(color: ColorConstant.white, fontSize: 13)),
                    ),
                  );
                },
              ),
            ),
          if (home.categories.isNotEmpty) ...[
            const _RailTitle('Categories'),
            SizedBox(
              height: 92,
              child: ListView.separated(
                padding: const EdgeInsets.symmetric(horizontal: 16),
                scrollDirection: Axis.horizontal,
                itemCount: home.categories.length,
                separatorBuilder: (_, _) => const SizedBox(width: 12),
                itemBuilder: (context, index) {
                  final category = home.categories[index];
                  return InkWell(
                    onTap: () => Navigator.pushNamed(context, AppRoutes.categoryVideos, arguments: category),
                    child: Column(
                      children: [
                        ClipRRect(
                          borderRadius: BorderRadius.circular(12),
                          child: SizedBox(
                            width: 64,
                            height: 64,
                            child: AppImage(url: category.imageUrl, icon: Icons.grid_view_rounded),
                          ),
                        ),
                        const SizedBox(height: 6),
                        SizedBox(
                          width: 72,
                          child: Text(
                            category.name,
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                            textAlign: TextAlign.center,
                            style: const TextStyle(color: ColorConstant.white, fontSize: 11),
                          ),
                        ),
                      ],
                    ),
                  );
                },
              ),
            ),
          ],
          _Rail(title: 'New Videos', videos: home.videos, onPlay: onPlay),
          if (paid.isNotEmpty) _Rail(title: 'Paid', videos: paid, onPlay: onPlay),
          if (free.isNotEmpty) _Rail(title: 'Free', videos: free, onPlay: onPlay),
          if (bottomBanner != null)
            Padding(
              padding: const EdgeInsets.fromLTRB(16, 18, 16, 0),
              child: ClipRRect(
                borderRadius: BorderRadius.circular(16),
                child: InkWell(
                  onTap: featured == null ? null : () => needsPlan ? onSubscribe() : onPlay(featured),
                  child: AspectRatio(
                    aspectRatio: 16 / 7,
                    child: Stack(
                      fit: StackFit.expand,
                      children: [
                        AppImage(url: bottomBanner.imageUrl, icon: Icons.image_outlined),
                        const DecoratedBox(
                          decoration: BoxDecoration(
                            gradient: LinearGradient(
                              begin: Alignment.centerLeft,
                              end: Alignment.centerRight,
                              colors: [Color(0xE6010A0F), Color(0x33010A0F)],
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              ),
            ),
        ],
      ),
    );
  }
}

class _Hero extends StatelessWidget {
  const _Hero({
    required this.banner,
    required this.video,
    required this.needsPlan,
    required this.onPlay,
    required this.onSubscribe,
  });

  final BannerItem banner;
  final VideoItem? video;
  final bool needsPlan;
  final ValueChanged<VideoItem> onPlay;
  final VoidCallback onSubscribe;

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      height: 360,
      child: Stack(
        fit: StackFit.expand,
        children: [
          AppImage(url: banner.imageUrl, icon: Icons.movie_outlined),
          const DecoratedBox(
            decoration: BoxDecoration(
              gradient: LinearGradient(
                begin: Alignment.topCenter,
                end: Alignment.bottomCenter,
                colors: [Color(0x66010A0F), Color(0xF2010A0F)],
              ),
            ),
          ),
          Padding(
            padding: const EdgeInsets.fromLTRB(20, 24, 20, 20),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisAlignment: MainAxisAlignment.end,
              children: [
                if (video?.categoryName != null)
                  Text(video!.categoryName!, style: const TextStyle(color: ColorConstant.lightGrayIcCl, fontSize: 13)),
                const SizedBox(height: 8),
                Text(
                  banner.title,
                  style: const TextStyle(color: ColorConstant.white, fontFamily: FontsStyle.bold, fontSize: 28),
                ),
                if (video != null) ...[
                  const SizedBox(height: 8),
                  Text(
                    [
                      if (video!.durationLabel.isNotEmpty) video!.durationLabel,
                      video!.isPaid ? 'Paid' : 'Free',
                    ].join('  ·  '),
                    style: const TextStyle(color: ColorConstant.lightGrayIcCl, fontSize: 13),
                  ),
                ],
                if (video?.description != null && video!.description!.isNotEmpty) ...[
                  const SizedBox(height: 8),
                  Text(
                    video!.description!,
                    maxLines: 3,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(color: ColorConstant.lightGrayIcCl, height: 1.35),
                  ),
                ],
                const SizedBox(height: 16),
                Align(
                  alignment: Alignment.centerLeft,
                  child: SizedBox(
                    width: needsPlan ? 200 : 160,
                    child: CustomButton(
                      text: needsPlan ? 'Get Subscription' : 'Play Now',
                      icon: needsPlan ? Icons.lock_outline_rounded : Icons.play_arrow_rounded,
                      height: 44,
                      onPressed: video == null ? null : () => needsPlan ? onSubscribe() : onPlay(video!),
                    ),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _RailTitle extends StatelessWidget {
  const _RailTitle(this.title);

  final String title;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.fromLTRB(16, 18, 16, 10),
      child: Text(
        title,
        style: const TextStyle(color: ColorConstant.white, fontFamily: FontsStyle.bold, fontSize: 18),
      ),
    );
  }
}

class _Rail extends StatelessWidget {
  const _Rail({required this.title, required this.videos, required this.onPlay});

  final String title;
  final List<VideoItem> videos;
  final ValueChanged<VideoItem> onPlay;

  @override
  Widget build(BuildContext context) {
    if (videos.isEmpty) return const SizedBox.shrink();
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        _RailTitle(title),
        SizedBox(
          height: 230,
          child: ListView.separated(
            padding: const EdgeInsets.symmetric(horizontal: 16),
            scrollDirection: Axis.horizontal,
            itemCount: videos.length,
            separatorBuilder: (_, _) => const SizedBox(width: 10),
            itemBuilder: (context, index) => VideoPoster(video: videos[index], onTap: () => onPlay(videos[index])),
          ),
        ),
      ],
    );
  }
}

class _Search extends StatelessWidget {
  const _Search({
    required this.controller,
    required this.categories,
    required this.selected,
    required this.results,
    required this.onChanged,
    required this.onCategory,
    required this.onPlay,
  });

  final TextEditingController controller;
  final List<CategoryItem> categories;
  final String? selected;
  final List<VideoItem> results;
  final VoidCallback onChanged;
  final ValueChanged<String?> onCategory;
  final ValueChanged<VideoItem> onPlay;

  @override
  Widget build(BuildContext context) {
    return SafeArea(
      child: Column(
        children: [
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 12, 16, 8),
            child: TextField(
              controller: controller,
              onChanged: (_) => onChanged(),
              style: const TextStyle(color: ColorConstant.white),
              decoration: InputDecoration(
                hintText: 'Search videos',
                hintStyle: const TextStyle(color: ColorConstant.hintTextCl),
                prefixIcon: const Icon(Icons.search, color: ColorConstant.lightGrayIcCl),
                filled: true,
                fillColor: ColorConstant.surfaceCl,
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none),
              ),
            ),
          ),
          SizedBox(
            height: 42,
            child: ListView(
              scrollDirection: Axis.horizontal,
              padding: const EdgeInsets.symmetric(horizontal: 16),
              children: [
                _Chip(label: 'All', selected: selected == null, onTap: () => onCategory(null)),
                ...categories.map(
                  (category) => Padding(
                    padding: const EdgeInsets.only(left: 8),
                    child: _Chip(
                      label: category.name,
                      selected: selected == category.name,
                      onTap: () => onCategory(category.name),
                    ),
                  ),
                ),
              ],
            ),
          ),
          Expanded(
            child: results.isEmpty
                ? const Center(
                    child: Text('No videos found', style: TextStyle(color: ColorConstant.lightGrayIcCl)),
                  )
                : GridView.builder(
                    padding: const EdgeInsets.fromLTRB(16, 8, 16, 16),
                    gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                      crossAxisCount: 3,
                      childAspectRatio: 0.62,
                      crossAxisSpacing: 10,
                      mainAxisSpacing: 10,
                    ),
                    itemCount: results.length,
                    itemBuilder: (context, index) =>
                        VideoPoster(width: double.infinity, video: results[index], onTap: () => onPlay(results[index])),
                  ),
          ),
        ],
      ),
    );
  }
}

class _Chip extends StatelessWidget {
  const _Chip({required this.label, required this.selected, required this.onTap});

  final String label;
  final bool selected;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(20),
      child: Container(
        alignment: Alignment.center,
        padding: const EdgeInsets.symmetric(horizontal: 14),
        decoration: BoxDecoration(
          color: selected ? ColorConstant.primaryCl : Colors.transparent,
          borderRadius: BorderRadius.circular(20),
          border: Border.all(color: selected ? ColorConstant.primaryCl : ColorConstant.lightBorderCl),
        ),
        child: Text(label, style: const TextStyle(color: ColorConstant.white, fontSize: 13)),
      ),
    );
  }
}
