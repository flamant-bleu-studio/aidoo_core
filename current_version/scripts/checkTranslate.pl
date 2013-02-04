#!/usr/bin/perl
use strict;
use warnings;
use File::Copy::Recursive qw(fcopy rcopy dircopy fmove rmove dirmove);

#Check sur le nombre d'arguments (1 minimum)
($#ARGV>=0) || die "\nInsufficient number of parameters\nSyntax is: perl checkTranslate.pl name_module[string] \n\n";

if ($ARGV[0] eq "help")
{
	print "\n";
	print "---------------------\n";
	print "- checkTranslate.pl -\n";
	print "---------------------\n";
	print "* This script take at least 1 arguments (name of the translate module)\n";
	print "* This script was made to check all translatable values and put them to language file (fr.php, en.php) for the backoffice of Aïdoo \n";
	print "* This script recognize {t}something{/t}, _t('something') and _t(\"something\")\n\n";
}
else 
{
	translateModule();
}

sub uniq {
    return keys %{{ map { $_ => 1 } @_ }};
}

sub GetFilesList
{
        my $Path = $_[0];
        my $FileFound;
        my @FilesList=();

        # Lecture de la liste des fichiers
        opendir (my $FhRep, $Path)
                or die "Impossible d'ouvrir le repertoire $Path\n";
        my @Contenu = grep { !/^\.\.?$/ } readdir($FhRep);
        closedir ($FhRep);

        foreach my $FileFound (@Contenu) {
                # Traitement des fichiers
                if ( -f "$Path/$FileFound") {
                        push ( @FilesList, "$Path/$FileFound" );
                }
                # Traitement des repertoires
                elsif ( -d "$Path/$FileFound") {
                        # Boucle pour lancer la recherche en mode recursif
                        push (@FilesList, GetFilesList("$Path/$FileFound") );
                }

        }
        return @FilesList;
}

sub translateModule
{
	#Variables
	my $module_name = $ARGV[0];
	my $module_path = "../application/modules/$module_name";
	my @array = ();
	my $counter;

	#Boucle sur tout les fichiers et sous fichiers

	#---------------------------
	# VIEWS
	#---------------------------
	my @Files = GetFilesList ("$module_path/views");
	foreach my $File  (@Files) {
		open(FILE_IN,$File);
		my @contenu = <FILE_IN>;
		close(FILE_IN);
		 
		foreach my $ligne (@contenu)
		{	
			my @occur =  split('{/t}', $ligne);
 			
			foreach my $split (@occur){
				if ($split =~ /{t}(.*)$/){
					push ( @array, $1 );				
				}
			}
		}
		close(FILE_OUT);
	}

	#---------------------------
	# CONTROLLERS 
	#---------------------------
	@Files = GetFilesList ("$module_path/controllers");
	foreach my $File  (@Files) {
		open(FILE_IN,$File);
		my @contenu = <FILE_IN>;
		close(FILE_IN);
		 
		foreach my $ligne (@contenu)
		{	
			my @occur =  split('[\'|"]\)', $ligne);
 			
			foreach my $split (@occur){
				if ($split =~ /_t\([\'|"](.*)$/){
					push ( @array, $1 );					
				}
			}
		}
		close(FILE_OUT);
	}

	#---------------------------
	# FORMS
	#---------------------------
	@Files = GetFilesList ("$module_path/forms");
	foreach my $File  (@Files) {
		open(FILE_IN,$File);
		my @contenu = <FILE_IN>;
		close(FILE_IN);
		 
		foreach my $ligne (@contenu)
		{	
			my @occur =  split('[\'|"]\)', $ligne);
 			
			foreach my $split (@occur){
				if ($split =~ /_t\([\'|"](.*)$/){
					push ( @array, $1 );					
				}
			}
		}
		close(FILE_OUT);
	}

	print "-------------------------\n";
	print "Mots à traduire trouvés : \n";
	print "-------------------------\n";
	#On rend le tableau avec des valeurs unique (pas besoin d'inserer trois fois la même valeur)	
	@array = uniq(@array);
	foreach (@array) {
 		print "* " . $_ . "\n";
 	} 

	my $quote_array;
	my @array_fr = @array;
	my @array_en = @array;

	#--------------------------------------------------------------------
	#--------------------------------------------------------------------
	#	FRANCAIS
	#--------------------------------------------------------------------
	#--------------------------------------------------------------------

	$counter = 0;

	# On récupère le contenu
	open(DESCR,"$module_path/lang/back/fr.php");
	my @contenu = <DESCR>;

	# On ouvre en écriture
	close(DESCR);
	open(DESCR,">$module_path/lang/back/fr.php");

	# On rempli le fichier de lang FR 
	foreach my $ligne (@contenu)
	{
		# Si on trouve déjà l'équivalence dans le fichier on le retire du tableau
		foreach (@array_fr) {
			$quote_array = quotemeta($_);
			if ($ligne =~ /\'$quote_array\'/){
				my $index = 0;
				$index++ until $array_fr[$index] eq $_;
				splice(@array_fr, $index, 1);
			} 
		}
		
		# Si c'est la derniere ligne on ajoute toutes les autre occurances dans le tableau
		if ($ligne =~ /\);/){
			foreach my $translate (@array_fr) {
				if ($counter == 0){
					print DESCR ',';
				}
				$translate =~ s/\'/\\\'/g;
				# Si c'est la derniere ligne on ne met pas de virgule de fin
				if (++$counter == scalar(@array_fr)){
					print DESCR "'$translate' => ''\n";
				} else {
					print DESCR "'$translate' => '',\n";
				}
			}
		print DESCR ');';
		} else {
			print DESCR $ligne;
		}
	}
	close(DESCR);
	
	#--------------------------------------------------------------------
	#--------------------------------------------------------------------
	#	ANGLAIS
	#--------------------------------------------------------------------
	#--------------------------------------------------------------------

	$counter = 0;

	# On récupère le contenu
	open(DESCR,"$module_path/lang/back/en.php");
	@contenu = <DESCR>;

	# On ouvre en écriture
	close(DESCR);
	open(DESCR,">$module_path/lang/back/en.php");

	# On rempli le fichier de lang FR 
	foreach my $ligne (@contenu)
	{
		# Si on trouve déjà l'équivalence dans le fichier on le retire du tableau
		foreach (@array_en) {
			$quote_array = quotemeta($_);
			if ($ligne =~ /\'$quote_array\'/){
				my $index = 0;
				$index++ until $array_en[$index] eq $_;
				splice(@array_en, $index, 1);
			} 
		}
		
		# Si c'est la derniere ligne on ajoute toutes les autre occurances dans le tableau
		if ($ligne =~ /\);/){
			foreach my $translate (@array_en) {
				if ($counter == 0){
					print DESCR ',';
				}
				$translate =~ s/\'/\\\'/g;
				# Si c'est la derniere ligne on ne met pas de virgule de fin
				if (++$counter == scalar(@array_en)){
					print DESCR "'$translate' => '$translate'\n";
				} else {
					print DESCR "'$translate' => '$translate',\n";
				}
			}
		print DESCR ');';
		} else {
			print DESCR $ligne;
		}
	}
	close(DESCR);
}

